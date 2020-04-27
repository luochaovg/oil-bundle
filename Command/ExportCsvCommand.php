<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Csv;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Console\Input\InputOption;

abstract class ExportCsvCommand extends RecursionSqlCommand
{
    /**
     * @var int
     */
    protected $limit = 50;

    /**
     * @var bool
     */
    protected $handlerByMultiple = true;

    /**
     * @var bool
     */
    protected $hasCnText = true;

    /**
     * @return array
     */
    public function args(): array
    {
        return array_merge(
            parent::args(),
            ['csv' => [null, InputOption::VALUE_REQUIRED, 'The csv file']]
        );
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'export-csv',
            'info'    => 'Export to csv',
        ];
    }

    /**
     * @return array
     */
    public function header(): array
    {
        return [];
    }

    /**
     * Handler
     *
     * @param array $record
     *
     * @return int|bool
     */
    public function handler(array $record)
    {
        if ($this->hasCnText) {
            setlocale(LC_ALL, 'zh_CN');
        }

        if ($this->page == 1) {
            $header = $this->header();
            $keys = array_keys(current($record));
            if (empty($header)) {
                $_header = Helper::arrayMap(
                    $keys,
                    function ($val) {
                        return Helper::stringToLabel($val);
                    }
                );
            } else {
                $_header = [];
                foreach ($keys as $key) {
                    $_header[] = $header[$key] ?? Helper::stringToLabel($key);
                }
            }
            array_unshift($record, $_header);
        }

        $this->csvWriter($record);

        return count($record);
    }

    /**
     * Csv writer
     *
     * @param array $list
     *
     * @throws
     */
    protected function csvWriter(array $list)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Csv();
            if (empty($this->params->csv)) {
                $this->params->csv = Abs::TMP_PATH . '/' . time() . '.csv';
            }

            @unlink($this->params->csv);
            fopen($this->params->csv, "w");

            $instance->setCsvFile($this->params->csv);
        }

        $args = Helper::pageArgs(['page' => $this->page, 'limit' => intval($this->params->limit)]);
        $instance->writer($list, $args['offset']);
    }
}