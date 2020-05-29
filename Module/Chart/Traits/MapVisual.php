<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait MapVisual
{
    /**
     * @var bool
     */
    protected $mapVisual = true;

    /**
     * @return bool
     */
    public function isMapVisual(): bool
    {
        return $this->mapVisual;
    }

    /**
     * @param bool $mapVisual
     */
    public function setMapVisual(bool $mapVisual = true): void
    {
        $this->mapVisual = $mapVisual;
    }
}