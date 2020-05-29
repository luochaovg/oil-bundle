<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Chart\Traits;

class Pie extends Chart
{
    use Traits\ShowLabel,
        Traits\TipsTpl,
        Traits\LabelTpl,
        Traits\Radius;

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setTooltip(
            [
                'trigger' => 'item',
                'formatter' => $this->getTipsTpl(),
            ]
        );
        $this->setLegendTitle($this->getDataField());
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param array $item
     *
     * @return array
     */
    protected function buildSeries(string $name, array $item): array
    {
        return [
            'selectedMode' => 'single',
            'center' => ['50%', '50%'],
            'radius' => $this->getRadius() ?: [0, '80%'],
            'label' => [
                'normal' => [
                    'show' => $this->isShowLabel(),
                    'formatter' => $this->getLabelTpl(),
                    'backgroundColor' => '#fff',
                    'borderColor' => '#eee',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'padding' => [0, 7],
                    'rich' => [
                        'a' => [
                            'color' => '#aaa',
                            'lineHeight' => 22,
                            'align' => 'center',
                        ],
                        'hr' => [
                            'borderColor' => '#eee',
                            'width' => '100%',
                            'borderWidth' => 0.5,
                            'height' => 0,
                        ],
                        'b' => [
                            'fontSize' => 16,
                            'lineHeight' => 33,
                        ],
                        'per' => [
                            'color' => '#eee',
                            'backgroundColor' => '#334455',
                            'padding' => [4, 4],
                            'borderRadius' => 4,
                        ],
                    ],
                ],
            ],
            'itemStyle' => [
                'emphasis' => [
                    'shadowBlur' => 10,
                    'shadowOffsetX' => 0,
                    'shadowColor' => 'rgba(0, 0, 0, 0.5)',
                ],
            ],
        ];
    }
}