<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Chart\Traits;

class Map extends Chart
{
    use Traits\MapKey,
        Traits\MapColor,
        Traits\MinValue,
        Traits\MaxValue;

    /**
     * @return string
     */
    protected function type(): string
    {
        return 'map';
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setSelectedMode(self::SELECTED_MODE_SINGLE)
            ->setTooltip(['trigger' => 'item'])
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

        $this->setMinValue(min(min($values), $this->getMinValue()));
        $this->setMaxValue(max(max($values), $this->getMaxValue()));

        return [
            'mapType'    => $this->getMapKey(),
            'roam'       => false,
            'zoom'       => 1,
            'scaleLimit' => [
                'min' => 1,
                'max' => 3,
            ],
            'label'      => [
                'normal'   => ['show' => false],
                'emphasis' => ['show' => false],
            ],
            'itemStyle'  => [
                'emphasis' => [
                    'areaColor'     => '#FFDEAD',
                    'shadowOffsetX' => 0,
                    'shadowOffsetY' => 0,
                    'shadowBlur'    => 20,
                    'borderWidth'   => 0,
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
                'show'       => !$this->isMobile(),
                'min'        => $this->getMinValue(),
                'max'        => $this->getMaxValue(),
                'left'       => '10%',
                'bottom'     => '10%',
                'calculable' => true,
                'inRange'    => ['color' => $this->getMapColor()],
            ];
        }

        return $option;
    }
}