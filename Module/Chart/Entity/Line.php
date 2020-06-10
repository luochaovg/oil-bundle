<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Component\Helper;
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
    protected $toolbox = [
        'feature' => [
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
            'restore'   => [
                'title' => 'Reset',
            ],
        ],
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
        if ($seriesExtra = current($this->getSeriesExtra())) {
            if (isset($seriesExtra['stack'])) {
                $this->setTooltipTpl('fn:TooltipStack');
            }
        }

        if ($this->isPointSenseReverse()) {
            $this->setPointField('max.itemStyle.color', $this->getPointBad());
            $this->setPointField('min.itemStyle.color', $this->getPointGood());
        } else {
            $this->setPointField('max.itemStyle.color', $this->getPointGood());
            $this->setPointField('min.itemStyle.color', $this->getPointBad());
        }

        $this->setAxisXTitle($this->getDataField())
            ->setLegendTitle(array_keys($this->getDataList()))
            ->setTooltipField('formatter', $this->getTooltipTpl())
            ->setPoint(array_values($this->getPoint()))
            ->setLine(array_values($this->getLine()));

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
            'markPoint'  => [
                'data' => $this->moduleState('point') ? $this->getPoint() : null,
            ],
            'markLine'   => [
                'data'     => $this->moduleState('line') ? $this->getLine() : null,
                'symbol'   => ['none', 'none'],
                'label'    => [
                    'position' => 'insideStartTop',
                ],
                'emphasis' => [
                    'lineStyle' => [
                        'width' => 1,
                    ],
                ],
            ],
        ];
    }
}