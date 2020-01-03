<?php

namespace Leon\BswBundle\Component;

use SplFileObject;
use Exception;

class Csv
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var SplFileObject
     */
    private $spl;

    /**
     * Set csv file
     *
     * @param string $filePath
     *
     * @return void
     * @throws
     */
    public function setCsvFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("Csv file not exists {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new Exception("Csv file not readable {$filePath}");
        }

        if (!is_writable($filePath)) {
            throw new Exception("Csv file not writable {$filePath}");
        }

        $this->file = $filePath;
        $this->spl = new SplFileObject($this->file, 'r+');
    }

    /**
     * Reader
     *
     * @param int  $length
     * @param int  $start
     * @param bool $strict
     *
     * @return array
     */
    public function reader(int $length = 0, int $start = 0, bool $strict = true): array
    {
        $start = $start < 0 ? 0 : $start;
        $length = $length > 0 ? $length : $this->lines();

        $this->spl->setFlags(SplFileObject::READ_CSV);
        $this->spl->seek(0);
        $field = $this->spl->current();

        $this->spl->seek($start);
        $fieldCount = count($field);

        $list = [];
        while ($length-- && !$this->spl->eof()) {

            $row = $this->spl->current();
            $rowCount = count($row);

            if ($strict) {
                if ($fieldCount == $rowCount) {
                    $list[] = array_combine($field, $row);
                }
            } else {
                if ($rowCount < $fieldCount) {
                    $field = array_slice($field, 0, $rowCount);
                } elseif ($rowCount > $fieldCount) {
                    $row = array_slice($row, 0, $fieldCount);
                }
                $list[] = array_combine($field, $row);
            }

            $this->spl->next();
        }

        return array_filter($list);
    }

    /**
     * Writer
     *
     * @param array $list
     * @param int   $start
     */
    public function writer(array $list, int $start = 0)
    {
        $start = $start < 0 ? 0 : $start;
        $this->spl->seek($start);

        foreach ($list as $item) {
            $this->spl->fputcsv($item);
        }
    }

    /**
     * Get lines
     *
     * @return int
     */
    public function lines(): int
    {
        static $line;

        if (!isset($line)) {
            $this->spl->seek(filesize($this->file));
            $line = $this->spl->key();
        }

        return $line;
    }
}