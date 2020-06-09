<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Chart\Traits;

class Pie extends Chart
{
    use Traits\ShowLabel,
        Traits\LabelTpl,
        Traits\Radius;

    /**
     * @return string
     */
    protected function type(): string
    {
        return 'pie';
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setLegendTitle($this->getDataField())
            ->moduleDisable('axisX')
            ->moduleDisable('axisY')
            ->setTooltip(['trigger' => 'item']);
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
            'selectedMode' => 'single',
            'center'       => ['50%', '50%'],
            'radius'       => $this->getRadius(),
            'label'        => [
                'normal' => [
                    'show'            => $this->isShowLabel(),
                    'formatter'       => $this->getLabelTpl(),
                    'backgroundColor' => '#fff',
                    'borderColor'     => '#eee',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                    'padding'         => [0, 7],
                    'rich'            => [
                        'a'   => [
                            'color'      => '#aaa',
                            'lineHeight' => 22,
                            'align'      => 'center',
                        ],
                        'hr'  => [
                            'borderColor' => '#eee',
                            'width'       => '100%',
                            'borderWidth' => 0.5,
                            'height'      => 0,
                        ],
                        'b'   => [
                            'fontSize'   => 16,
                            'lineHeight' => 33,
                        ],
                        'per' => [
                            'color'           => '#eee',
                            'backgroundColor' => '#334455',
                            'padding'         => [4, 4],
                            'borderRadius'    => 4,
                        ],
                    ],
                ],
            ],
            'itemStyle'    => [
                'emphasis' => [
                    'shadowBlur'    => 10,
                    'shadowOffsetX' => 0,
                    'shadowColor'   => 'rgba(0, 0, 0, 0.5)',
                ],
            ],
        ];
    }
}