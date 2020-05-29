<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;

class Pie extends Chart
{
    /**
     * @var bool
     */
    protected $showPieLabel = false;

    /**
     * @var string
     */
    protected $tipsPieTpl = "{a} <br/>{b}: {c} ({d}%)";

    /**
     * @var string
     */
    protected $labelPieTpl = "{a|{a}}\n{hr|}\n {b|{b}：}{c}  {per|{d}%} ";

    /**
     * @var array
     */
    protected $radius = [];

    /**
     * @var null
     */
    protected $xAxis = false;

    /**
     * @var null
     */
    protected $yAxis = false;

    /**
     * @return bool
     */
    public function isShowPieLabel(): bool
    {
        return $this->showPieLabel;
    }

    /**
     * @param bool $showPieLabel
     *
     * @return $this
     */
    public function setShowPieLabel(bool $showPieLabel)
    {
        $this->showPieLabel = $showPieLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getTipsPieTpl(): string
    {
        return $this->tipsPieTpl;
    }

    /**
     * @param string $tipsPieTpl
     *
     * @return $this
     */
    public function setTipsPieTpl(string $tipsPieTpl)
    {
        $this->tipsPieTpl = $tipsPieTpl;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelPieTpl(): string
    {
        return $this->labelPieTpl;
    }

    /**
     * @param string $labelPieTpl
     *
     * @return $this
     */
    public function setLabelPieTpl(string $labelPieTpl)
    {
        $this->labelPieTpl = $labelPieTpl;

        return $this;
    }

    /**
     * @return array
     */
    public function getRadius(): array
    {
        return $this->radius;
    }

    /**
     * @param array $radius
     *
     * @return $this
     */
    public function setRadius(array $radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setRadiusField(string $field, $value)
    {
        $this->radius[$field] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setTooltip(
            [
                'trigger'   => 'item',
                'formatter' => $this->getTipsPieTpl(),
            ]
        );
        $this->setLegendTitle($this->getField());
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
            'radius'       => $this->getRadius() ?: [0, '80%'],
            'label'        => [
                'normal' => [
                    'show'            => $this->isShowPieLabel(),
                    'formatter'       => $this->getLabelPieTpl(),
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