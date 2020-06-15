<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Chart\Traits;

class Map extends Chart
{
    use Traits\MapSource,
        Traits\MapColor,
        Traits\MapNameAlias,
        Traits\MinValue,
        Traits\MaxValue;

    /**
     * @var string
     */
    protected $type = 'map';

    /**
     * @var string
     */
    protected $tooltipTpl = '{b} - {c}';

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setSelectedMode(self::SELECTED_MODE_SINGLE)
            ->moduleDisable('legend', 'grid', 'axisX', 'axisY')
            ->setTooltipField('formatter', $this->getTooltipTpl())
            ->setTooltipField('trigger', 'item')
            ->setLegendTitle(array_keys($this->getDataList()));
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param array  $item
     *
     * @return array
     */
    protected function buildSeries(string $name, array $item): array
    {
        $values = array_column($item, 'value');

        $this->setMaxValue(max(max($values), $this->getMaxValue()));
        $this->setMinValue(min(min($values), $this->getMinValue()));

        return [
            'map'        => $this->getMapKey(),
            'mapType'    => $this->getMapKey(),
            'roam'       => true,
            'zoom'       => 1,
            'scaleLimit' => [
                'min' => 1,
                'max' => 10,
            ],
            'label'      => [
                'normal'   => ['show' => false],
                'emphasis' => ['show' => false],
            ],
            'nameMap'    => $this->getMapNameAlias(),
            'itemStyle'  => [
                'areaColor'   => 'rgba(240, 240, 240, .2)',
                'borderColor' => 'rgba(0, 0, 0, .5)',
                'emphasis'    => [
                    'areaColor'     => 'rgba(255, 165, 0, .3)',
                    'shadowOffsetX' => 0,
                    'shadowOffsetY' => 0,
                    'shadowBlur'    => 0,
                    'borderWidth'   => .5,
                    'shadowColor'   => 'rgba(0, 0, 0, .5)',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * @param array $option
     *
     * @return array
     */
    protected function rebuildOption(array $option): array
    {
        if ($this->moduleState('mapVisual')) {
            $option['visualMap'] = [
                'type'       => 'piecewise',
                'show'       => !$this->isMobile(),
                'min'        => $this->getMinValue(),
                'max'        => $this->getMaxValue(),
                'left'       => '5%',
                'bottom'     => '10%',
                'calculable' => true,
                'inRange'    => ['color' => $this->getMapColor()],
            ];
        }

        return $option;
    }
}