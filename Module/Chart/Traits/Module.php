<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Module
{
    /**
     * @var bool[]
     */
    protected $module = [
        'title'     => true,
        'tooltip'   => true,
        'toolbox'   => true,
        'legend'    => true,
        'grid'      => true,
        'axisX'     => true,
        'axisY'     => true,
        'zoom'      => true,
        'series'    => true,
        'line'      => true,
        'point'     => true,
        'mapVisual' => true,
    ];

    /**
     * @param string ...$names
     *
     * @return $this
     */
    public function moduleDisable(string ...$names)
    {
        foreach ($names as $name) {
            $this->module[$name] = false;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function moduleEnable(string $name)
    {
        $this->module[$name] = true;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function moduleState(string $name): bool
    {
        return $this->module[$name] ?? false;
    }
}