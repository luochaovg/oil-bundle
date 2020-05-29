<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Series
{
    /**
     * @var array
     */
    protected $series = [];

    /**
     * @return array
     */
    public function getSeries(): array
    {
        return $this->series;
    }

    /**
     * @param array $series
     *
     * @return $this
     */
    public function setSeries(array $series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSeriesField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->series[$field]);
        } else {
            $this->series[$field] = $value;
        }

        return $this;
    }
}