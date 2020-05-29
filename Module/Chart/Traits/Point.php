<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Point
{
    /**
     * @var array
     */
    protected $point = [
        'max' => [
            'type'      => 'max',
            'name'      => 'Max',
            'itemStyle' => ['color' => '#5FB878'],
        ],
        'min' => [
            'type'      => 'min',
            'name'      => 'Min',
            'itemStyle' => ['color' => '#FFB800'],
        ],
    ];

    /**
     * @return array
     */
    public function getPoint(): array
    {
        return $this->point;
    }

    /**
     * @param array $point
     *
     * @return $this
     */
    public function setPoint(array $point)
    {
        $this->point = $point;

        return $this;
    }
}