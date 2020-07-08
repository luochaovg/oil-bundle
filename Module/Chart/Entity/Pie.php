<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Chart\Traits;
use Leon\BswBundle\Module\Entity\Abs;

class Pie extends Chart
{
    use Traits\ShowLabel,
        Traits\LabelTpl,
        Traits\Radius;

    /**
     * @var string
     */
    protected $type = 'pie';

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setLegendTitle($this->getDataField())
            ->setTooltipField('trigger', 'item')
            ->setTooltipField('formatter', $this->getTooltipTpl())
            ->moduleDisable('axisX', 'axisY');
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
            'selectedMode' => Abs::SELECTOR_MODE_SINGLE,
            'center'       => ['50%', '50%'],
            'radius'       => $this->getRadius(),
            'label'        => [
                'normal' => [
                    'show'            => $this->isShowLabel(),
                    'formatter'       => $this->getLabelTpl(),
                    'backgroundColor' => 'rgba(255, 255, 255, .8)',
                    'borderColor'     => '#eee',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                    'padding'         => [2, 10],
                    'rich'            => [
                        'text' => [
                            'lineHeight' => 30,
                        ],
                        'hr'   => [
                            'borderColor' => '#eee',
                            'width'       => '100%',
                            'borderWidth' => 0.5,
                            'height'      => 0,
                        ],
                    ],
                ],
            ],
            'itemStyle'    => [
                'emphasis' => [
                    'shadowBlur'    => 8,
                    'shadowOffsetX' => 0,
                    'shadowColor'   => 'rgba(0, 0, 0, .5)',
                ],
            ],
        ];
    }
}