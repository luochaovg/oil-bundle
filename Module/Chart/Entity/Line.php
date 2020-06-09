<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Chart\Traits;

class Line extends Chart
{
    use Traits\Smooth,
        Traits\Point,
        Traits\Line;

    /**
     * @var array
     */
    protected $axisX = [
        'axisLine'    => [
            'lineStyle' => [
                'color' => '#666',
            ],
        ],
        'boundaryGap' => true,
    ];

    /**
     * @var array
     */
    protected $axisY = [
        'axisLine' => [
            'lineStyle' => [
                'color' => '#666',
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $tooltip = [
        'trigger'     => 'axis',
        'axisPointer' => [
            'type'        => 'shadow', // cross、shadow
            'label'       => [
                'backgroundColor' => 'rgba(150,150,150,0.5)',
            ],
            'lineStyle'   => [
                'color' => 'rgba(150,150,150,0.3)',
                'type'  => 'dashed',
            ],
            'crossStyle'  => [
                'color' => 'rgba(150,150,150,0.3)',
            ],
            'shadowStyle' => [
                'color' => 'rgba(150,150,150,0.1)',
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $featureLine = [
        'dataZoom'  => [
            'yAxisIndex' => 'none',
            'title'      => [
                'zoom' => 'Zoom',
                'back' => 'Reset',
            ],
        ],
        'magicType' => [
            'type'  => ['line', 'bar'],
            'title' => [
                'line' => 'Line',
                'bar'  => 'Bar',
            ],
        ],
        'restore'   => ['title' => 'Reset'],
    ];

    /**
     * @return string
     */
    protected function type(): string
    {
        return 'line';
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setAxisXTitle($this->getDataField())
            ->setLegendTitle(array_keys($this->getDataList()))
            ->setTooltipField('formatter', $this->getTooltipTpl())
            ->setPoint(array_values($this->getPoint()))
            ->setLine(array_values($this->getLine()));

        $feature = $this->mobile ? [] : $this->featureLine;
        $feature = array_merge($feature, $this->getFeature());
        $this->setFeature($feature);

        foreach ($this->getLegendTitle() as $key => $val) {
            $this->setLegendTitleField($key, strval($val));
        }
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
        return [
            'lineStyle'  => [
                'normal' => [
                    'width'         => 1.2,
                    'shadowColor'   => 'rgba(0, 0, 0, .4)',
                    'shadowBlur'    => 4,
                    'shadowOffsetY' => 4,
                ],
            ],
            'smooth'     => $this->isSmooth(),
            'symbolSize' => 6,
            'markPoint'  => ['data' => $this->moduleState('point') ? $this->getPoint() : null],
            'markLine'   => ['data' => $this->moduleState('line') ? $this->getLine() : null],
        ];
    }
}