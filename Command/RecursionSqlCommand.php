<?php

namespace Leon\BswBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Interfaces\CommandInterface;
use Leon\BswBundle\Repository\FoundationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;
use Exception;

abstract class RecursionSqlCommand extends Command implements CommandInterface
{
    use BswFoundation;

    /**
     * @var ObjectRepository|FoundationRepository|ObjectManager|EntityRepository
     */
    protected $repo;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var object
     */
    protected $_params;

    /**
     * @var object
     */
    protected $params;

    /**
     * @var int
     */
    protected $page = 0;

    /**
     * @var bool
     */
    protected $handlerByMultiple = false;

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'limit' => [null, InputOption::VALUE_OPTIONAL, 'Limit of list handler', 10],
            'args'  => [null, InputOption::VALUE_OPTIONAL, 'Extra arguments'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'sql',
            'info'    => 'Recursion execute sql',
        ];
    }

    /**
     * @return string
     */
    public function entity(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function filter(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function lister(): array
    {
        return [];
    }

    /**
     * @param object $params
     *
     * @return object
     */
    public function params($params)
    {
        return $params;
    }

    /**
     * @return bool
     */
    public function forbid(): bool
    {
        return false;
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    abstract public function handler(array $record): bool;

    /**
     * @return void
     */
    public function done()
    {
        $this->output->writeln("<info> Sql recursion done\n </info>");
    }

    /**
     * @param int $limit
     * @param int $page
     * @param int $pageDone
     * @param int $pageCount
     * @param int $totalSuccess
     * @param int $total
     *
     * @return string
     */
    public function process(
        int $limit,
        int $page,
        int $pageDone,
        int $pageCount,
        int $totalSuccess,
        int $total
    ): string {
        $process = number_format($totalSuccess / $total * 100, 2);

        $pageInfo = "page {$page}";
        $currentInfo = "current done {$pageDone}/{$pageCount}";
        $totalInfo = "total done {$totalSuccess}/{$total}";
        $processInfo = "process {$process}%";

        return "<info> {$pageInfo}, {$currentInfo}, {$totalInfo}, {$processInfo}. </info>";
    }

    /**
     * Execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        if (method_exists($this, $fn = Abs::FN_INIT)) {
            $this->{$fn}();
        }

        $this->_params = $this->options($input);
        $this->params = (object)$this->_params;
        $this->params->args = (object)Helper::parseJsonString(base64_decode($this->params->args));
        $this->params = $this->params($this->params);

        if ($this->forbid()) {
            return;
        }

        $this->logic($this->params->limit);
        $this->done();
    }

    /**
     * @param int $limit
     * @param int $page
     * @param int $totalSuccess
     *
     * @return int
     * @throws
     */
    protected function logic(int $limit, int $page = 1, int $totalSuccess = 0): int
    {
        if ($limit < 2) {
            throw new InvalidArgumentException('Arguments `limit` should be integer and gte 2');
        }

        $paging = true;
        $query = Helper::pageArgs(compact('paging', 'page', 'limit'));

        if ($entity = $this->entity()) {

            $this->repo = $this->repo($entity);
            $filter = array_merge($this->filter(), $query);
            $result = $this->repo->lister($filter);

        } elseif ($result = $this->lister()) {
            $result = $this->web->manualListForPagination($result, $query);
        } else {
            $result = [];
        }

        $this->page = $page;
        if ($page === 1 && empty($result['items'])) {
            return 0;
        }

        $pageDone = 0;
        try {
            if ($this->handlerByMultiple) {
                $pageDone += ($this->handler($result['items']) ? count($result['items']) : 0);
            } else {
                foreach ($result['items'] as $record) {
                    $pageDone += ($this->handler($record) ? 1 : 0);
                }
            }
        } catch (Exception $e) {
            $this->output->writeln("<error> {$e->getMessage()} </error>");

            return 0;
        }

        $totalSuccess += $pageDone;
        $pageCount = count($result['items']);

        $this->output->writeln(
            $this->process($limit, $page, $pageDone, $pageCount, $totalSuccess, $result['total_item'])
        );

        if ($limit == $pageCount) {
            return $this->logic($limit, ++$page, $totalSuccess);
        }

        return $page;
    }
}