<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait MaxValue
{
    /**
     * @var integer
     */
    protected $maxValue = 0;

    /**
     * @return int
     */
    public function getMaxValue(): int
    {
        return $this->maxValue;
    }

    /**
     * @param int $maxValue
     *
     * @return $this
     */
    public function setMaxValue(int $maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }
}