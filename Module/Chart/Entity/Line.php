<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;

class Line extends Chart
{
    /**
     * @var bool
     */
    protected $smooth = true;

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
     * @var array
     */
    protected $line = [
        'avg' => [
            'type' => 'average',
            'name' => 'avg',
        ],
    ];

    /**
     * @return bool
     */
    public function isSmooth(): bool
    {
        return $this->smooth;
    }

    /**
     * @param bool $smooth
     *
     * @return $this
     */
    public function setSmooth(bool $smooth)
    {
        $this->smooth = $smooth;

        return $this;
    }

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

    /**
     * @return array
     */
    public function getLine(): array
    {
        return $this->line;
    }

    /**
     * @param array $line
     *
     * @return $this
     */
    public function setLine(array $line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setXAxis(
            [
                'axisLine'    => [
                    'lineStyle' => [
                        'color' => '#666',
                    ],
                ],
                'boundaryGap' => true,
            ]
        );

        $this->setYAxis(
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

        $this->setXAxisTitle($this->getDataField());
        $this->setLegendTitle(array_keys($this->getDataList()));

        foreach ($this->getLegendTitle() as $key => $val) {
            $this->setLegendTitleField($key, strval($val));
        }

        $this->setTooltipField('formatter', $this->getTooltipFmt());
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