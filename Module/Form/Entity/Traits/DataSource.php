<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait DataSource
{
    /**
     * @var array|string
     */
    protected $dataSource = [];

    /**
     * @return string
     */
    public function getDataSource(): string
    {
        if (is_string($this->dataSource)) {
            return $this->dataSource;
        }

        return Helper::jsonStringify(Helper::stringValues($this->dataSource));
    }

    /**
     * @return array
     */
    public function getDataSourceArray(): array
    {
        if (is_string($this->dataSource)) {
            return [];
        }

        return $this->dataSource;
    }

    /**
     * @param array|string $dataSource
     *
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }
}