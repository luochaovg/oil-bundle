<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Csv;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Interfaces\CommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;
use Exception;
use function GuzzleHttp\Psr7\parse_query;

abstract class ImportCsvCommand extends Command implements CommandInterface
{
    use BswFoundation;

    /**
     * @var object
     */
    protected $params;

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'csv'       => [null, InputOption::VALUE_REQUIRED, 'The csv file'],
            'limit'     => [null, InputOption::VALUE_OPTIONAL, 'Limit of list handler', 100],
            'data-line' => [null, InputOption::VALUE_OPTIONAL, 'The line number of data', 2],
            'args'      => [null, InputOption::VALUE_OPTIONAL, 'Extra arguments'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'import-csv',
            'info'    => 'Import from csv',
        ];
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
     * @param object $params
     *
     * @return bool
     */
    public function forbid($params): bool
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
     * @param OutputInterface $output
     */
    public function done(OutputInterface $output)
    {

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
     * @param OutputInterface $output
     *
     * @return mixed
     */
    public function empty(OutputInterface $output)
    {
        return $output->writeln('The csv file is empty');
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
        if (method_exists($this, $fn = Abs::FN_INIT)) {
            $this->{$fn}();
        }

        $this->params = (object)$this->options($input);
        $this->params->args = (object)Helper::parseJsonString(base64_decode($this->params->args));
        $this->params = $this->params($this->params);

        if ($this->forbid($this->params)) {
            return;
        }

        if ($this->logic($this->params->limit, $output, $this->params->csv)) {
            $this->done($output);
            $output->writeln("<info> \n import done\n </info>");
        }
    }

    /**
     * Csv reader
     *
     * @param string $csv
     * @param int    $page
     * @param int    $limit
     *
     * @return array
     * @throws
     */
    protected function csvReader(string $csv, int $page, int $limit): array
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Csv();
            $instance->setCsvFile($csv);
        }

        $total = $instance->lines();
        $beginLine = $this->params->{'data-line'};

        $args = Helper::pageArgs(['page' => $page, 'limit' => $limit]);
        $items = $instance->reader($args['limit'], $args['offset'] + $beginLine - 1, false);

        return [$total - $beginLine + 1, $items];
    }

    /**
     * @param int             $limit
     * @param OutputInterface $output
     * @param string          $csv
     * @param int             $page
     * @param int             $totalSuccess
     *
     * @return int
     * @throws
     */
    protected function logic(
        int $limit,
        OutputInterface $output,
        string $csv,
        int $page = 1,
        int $totalSuccess = 0
    ): int {

        if ($limit < 2) {
            throw new InvalidArgumentException('Arguments `limit` should be integer and gte 2');
        }

        [$total, $items] = $this->csvReader($csv, $page, $limit);
        if ($page === 1 && empty($items)) {
            return $this->empty($output);
        }

        $pageDone = 0;
        try {
            foreach ($items as $record) {
                $record = Helper::numericValues($record);
                $pageDone += ($this->handler($record) ? 1 : 0);
            }
        } catch (Exception $e) {
            $output->writeln("<error> {$e->getMessage()} </error>");

            return 0;
        }

        $totalSuccess += $pageDone;
        $pageCount = count($items);

        $output->writeln($this->process($limit, $page, $pageDone, $pageCount, $totalSuccess, $total));

        if ($limit == $pageCount) {
            return $this->logic($limit, $output, $csv, ++$page, $totalSuccess);
        }

        return $page;
    }
}