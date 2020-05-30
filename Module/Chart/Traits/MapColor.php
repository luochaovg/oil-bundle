<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait MapColor
{
    /**
     * @var array
     */
    protected $mapColor = ['white', '#009688'];

    /**
     * @return array
     */
    public function getMapColor(): array
    {
        return $this->mapColor;
    }

    /**
     * @param array $mapColor
     *
     * @return $this
     */
    public function setMapColor(array $mapColor)
    {
        $this->mapColor = $mapColor;

        return $this;
    }
}