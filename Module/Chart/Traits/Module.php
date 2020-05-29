<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Module
{
    /**
     * @var bool[]
     */
    protected $module = [
        'title' => true,
        'tooltip' => true,
        'toolbox' => true,
        'legend' => true,
        'grid' => true,
        'axisX' => true,
        'axisY' => true,
        'zoom' => true,
        'series' => true,
    ];

    /**
     * @param string $name
     */
    public function moduleDisable(string $name)
    {
        $this->module[$name] = false;
    }

    /**
     * @param string $name
     */
    public function moduleEnable(string $name)
    {
        $this->module[$name] = true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function moduleState(string $name): bool
    {
        return $this->module[$name] ?? false;
    }
}