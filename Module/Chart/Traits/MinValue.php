<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait MinValue
{
    /**
     * @var integer
     */
    protected $minValue = 0;

    /**
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->minValue;
    }

    /**
     * @param int $minValue
     *
     * @return $this
     */
    public function setMinValue(int $minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }
}