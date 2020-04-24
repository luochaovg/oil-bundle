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

abstract class ImportCsvCommand extends Command implements CommandInterface
{
    use BswFoundation;

    /**
     * @var object
     */
    protected $_params;

    /**
     * @var object
     */
    protected $params;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var bool
     */
    protected $process = 'page {PageNow}/{PageTotal}, round {RoundSuccess}/{RoundTotal}, total {RecordSuccess}/{RecordTotal}, process {Process}%';

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
     * @return bool
     */
    public function forbid(): bool
    {
        return false;
    }

    /**
     * @param array $record
     *
     * @return int|bool
     */
    abstract public function handler(array $record);

    /**
     * @return void
     */
    public function done()
    {
        $this->output->writeln("<info> Csv import done\n </info>");
    }

    /**
     * @param int $limit
     * @param int $pageTotal
     * @param int $pageNow
     * @param int $roundTotal
     * @param int $roundSuccess
     * @param int $recordTotal
     * @param int $recordSuccess
     *
     * @return string
     */
    public function process(
        int $limit,
        int $pageTotal,
        int $pageNow,
        int $roundTotal,
        int $roundSuccess,
        int $recordTotal,
        int $recordSuccess
    ): string {
        $process = number_format(($pageNow * $limit) / $recordTotal * 100, 2);
        $info = str_replace(
            [
                '{Limit}',
                '{PageTotal}',
                '{PageNow}',
                '{RoundTotal}',
                '{RoundSuccess}',
                '{RecordTotal}',
                '{RecordSuccess}',
                '{Process}',
            ],
            [$limit, $pageTotal, $pageNow, $roundTotal, $roundSuccess, $recordTotal, $recordSuccess, $process],
            $this->process
        );

        return "<info> {$info} </info>";
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

        $this->logic($this->params->limit, $this->params->csv);
        $this->done();
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
     * @param int    $limit
     * @param string $csv
     * @param int    $pageNow
     * @param int    $recordSuccess
     *
     * @return int
     * @throws
     */
    protected function logic(int $limit, string $csv, int $pageNow = 1, int $recordSuccess = 0): int
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Arguments `limit` should be integer and gte 1');
        }

        [$recordTotal, $items] = $this->csvReader($csv, $pageNow, $limit);
        if (empty($items)) {
            return $pageNow === 1 ? 0 : $pageNow;
        }

        $roundSuccess = 0;
        $pageTotal = ceil($recordTotal / $limit);

        try {
            foreach ($items as $record) {
                $record = Helper::numericValues($record);
                $roundSuccess += ($this->handler($record) ? 1 : 0);
            }
        } catch (Exception $e) {
            $this->output->writeln("<error> {$e->getMessage()} </error>");

            return 0;
        }

        $recordSuccess += $roundSuccess;
        $roundTotal = count($items);

        if ($this->process) {
            $this->output->writeln(
                $this->process($limit, $pageTotal, $pageNow, $roundTotal, $roundSuccess, $recordTotal, $recordSuccess)
            );
        }

        if ($limit == $roundTotal) {
            return $this->logic($limit, $csv, ++$pageNow, $recordSuccess);
        }

        return $pageNow;
    }
}