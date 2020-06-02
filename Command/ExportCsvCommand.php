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
    protected $limit = 1000;

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
     * @param array $fields
     *
     * @return array
     */
    public function header(array $fields): array
    {
        $fieldsLabel = [];
        foreach ($fields as $field) {
            $fieldsLabel[$field] = $this->web->fieldLang(Helper::stringToLabel($field));
        }

        return $fieldsLabel;
    }

    /**
     * @param array $record
     *
     * @return array|false
     */
    public function handleRecord(array $record)
    {
        return $record;
    }

    /**
     * @param array $records
     *
     * @return array
     */
    public function handleAllRecord(array $records): array
    {
        return $records;
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
        static $keys, $_keys, $header;
        if (!isset($header)) {
            $keys = array_keys(current($record));
            $header = $this->header($keys);
            $_keys = array_keys($header);
        }

        if ($this->hasCnText) {
            setlocale(LC_ALL, 'zh_CN');
        }

        $record = $this->handleAllRecord($record);
        foreach ($record as $key => &$item) {
            $item = Helper::arrayPull($item, $_keys, false, '');
            $item = $this->handleRecord($item);
            if ($item === false) {
                unset($record[$key]);
            }
        }

        if ($this->page == 1) {
            $_header = [];
            foreach ($keys as $key) {
                if (!isset($header[$key])) {
                    continue;
                }
                $_header[] = $header[$key];
            }
            array_unshift($record, $_header);
        }

        $this->csvWriter($record);
        $total = count($record);

        return $this->page == 1 ? $total - 1 : $total;
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
                $this->params->csv = date('YmdHis') . '.csv';
            }

            if (strpos($this->params->csv, '/') === false) {
                $this->params->csv = Abs::TMP_PATH . '/' . $this->params->csv;
            }

            @unlink($this->params->csv);
            fopen($this->params->csv, "w");

            $instance->setCsvFile($this->params->csv);
        }

        $args = Helper::pageArgs(['page' => $this->page, 'limit' => intval($this->params->limit)]);
        $instance->writer($list, $args['offset']);
    }
}