<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait SeriesExtra
{
    /**
     * @var array
     */
    protected $seriesExtra = [];

    /**
     * @return array
     */
    public function getSeriesExtra(): array
    {
        return $this->seriesExtra;
    }

    /**
     * @param array $seriesExtra
     *
     * @return $this
     */
    public function setSeriesExtra(array $seriesExtra)
    {
        $this->seriesExtra = $seriesExtra;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSeriesExtraField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->seriesExtra[$field]);
        } else {
            $this->seriesExtra[$field] = $value;
        }

        return $this;
    }
}