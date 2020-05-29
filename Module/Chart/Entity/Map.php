<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;

class Map extends Chart
{
    /**
     * @var string
     */
    protected $map = 'china';

    /**
     * @var bool
     */
    protected $showVisualMap = true;

    /**
     * @var integer
     */
    protected $minValue = 0;

    /**
     * @var integer
     */
    protected $maxValue = 0;

    /**
     * @var array
     */
    protected $mapColor = ['white', '#009688'];

    /**
     * @return string
     */
    public function getMap(): string
    {
        return $this->map;
    }

    /**
     * @param string $map
     *
     * @return $this
     */
    public function setMap(string $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowVisualMap(): bool
    {
        return $this->showVisualMap;
    }

    /**
     * @param bool $showVisualMap
     */
    public function setShowVisualMap(bool $showVisualMap): void
    {
        $this->showVisualMap = $showVisualMap;
    }

    /**
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->minValue;
    }

    /**
     * @param int $minValue
     *
     * @return $this
     */
    public function setMinValue(int $minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxValue(): int
    {
        return $this->maxValue;
    }

    /**
     * @param int $maxValue
     *
     * @return $this
     */
    public function setMaxValue(int $maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * @return array
     */
    public function getMapColor(): array
    {
        return $this->mapColor;
    }

    /**
     * @param array $mapColor
     *
     * @return $this
     */
    public function setMapColor(array $mapColor)
    {
        $this->mapColor = $mapColor;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setMapColorField(string $field, $value)
    {
        $this->mapColor[$field] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        $this->setSelectedMode('single');
        $this->setTooltip(['trigger' => 'item']);
        $this->setLegendTitle(array_keys($this->getData()));
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
            'mapType'    => $this->getMap(),
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
                    'shadowColor'   => 'rgba(0, 0, 0, 0.5)',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * @param array $options
     *
     * @return array
     */
    protected function buildOptions(array $options): array
    {
        if ($this->isShowVisualMap()) {
            $options['visualMap'] = [
                'show'       => !$this->isMobile(),
                'min'        => $this->getMinValue(),
                'max'        => $this->getMaxValue(),
                'left'       => '10%',
                'bottom'     => '10%',
                'calculable' => true,
                'inRange'    => ['color' => $this->getMapColor()],
            ];
        }

        return $options;
    }
}