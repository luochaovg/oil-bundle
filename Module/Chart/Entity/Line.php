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
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setAxisX(
            [
                'axisLine'    => [
                    'lineStyle' => [
                        'color' => '#666',
                    ],
                ],
                'boundaryGap' => true,
            ]
        );

        $this->setAxisY(
            [
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#666',
                    ],
                ],
            ]
        );

        $this->setTooltip(
            [
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
            ]
        );

        $this->setFeature(
            array_merge(
                $this->mobile ? [] : [
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
                ],
                $this->feature
            )
        );

        $this->setAxisXTitle($this->getDataField());
        $this->setLegendTitle(array_keys($this->getDataList()));

        foreach ($this->getLegendTitle() as $key => $val) {
            $this->setLegendTitleField($key, strval($val));
        }

        $this->setTooltipField('formatter', $this->getTooltipTpl());
        $this->setPoint(array_values($this->getPoint()));
        $this->setLine(array_values($this->getLine()));
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
                    'width'         => 2,
                    'shadowColor'   => 'rgba(0,0,0,0.4)',
                    'shadowBlur'    => 8,
                    'shadowOffsetY' => 8,
                ],
            ],
            'smooth'     => $this->isSmooth(),
            'symbolSize' => 8,
            'markPoint'  => ['data' => $this->getPoint()],
            'markLine'   => ['data' => $this->getLine()],
        ];
    }
}