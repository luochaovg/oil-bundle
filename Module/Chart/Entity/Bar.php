<?php

namespace Leon\BswBundle\Module\Chart\Entity;

class Bar extends Line
{
    /**
     * @var string
     */
    protected $labelStackTpl = "{name|{a}} {c}";

    /**
     * @var int
     */
    protected $maxBarFixedWidth = 10;

    /**
     * @return string
     */
    public function getLabelStackTpl(): string
    {
        return $this->labelStackTpl;
    }

    /**
     * @param string $labelStackTpl
     *
     * @return $this
     */
    public function setLabelStackTpl(string $labelStackTpl)
    {
        $this->labelStackTpl = $labelStackTpl;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxBarFixedWidth(): int
    {
        return $this->maxBarFixedWidth;
    }

    /**
     * @param int $maxBarFixedWidth
     *
     * @return $this
     */
    public function setMaxBarFixedWidth(int $maxBarFixedWidth)
    {
        $this->maxBarFixedWidth = $maxBarFixedWidth;

        return $this;
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
        $series = parent::buildSeries($name, $item);

        $seriesExtra = $this->getSeriesExtra();
        $stackTpl = $this->getLabelStackTpl();

        if (!empty($seriesExtra[$name]['stack']) && $stackTpl) {
            $series['label'] = [
                'normal' => [
                    'show'          => true,
                    'position'      => 'insideBottom',
                    'distance'      => 15,
                    'align'         => 'center',
                    'verticalAlign' => 'middle',
                    'rotate'        => 0,
                    'formatter'     => $stackTpl,
                    'fontSize'      => 10,
                    'rich'          => [
                        'name' => [
                            'textBorderColor' => 'white',
                            'fontSize'        => 10,
                        ],
                    ],
                ],
            ];
        }

        if (!empty($seriesExtra[$name]['itemStyle'])) {
            $series['itemStyle'] = $seriesExtra[$name]['itemStyle'];
        }

        return $series;
    }

    /**
     * @inheritdoc
     *
     * @param array $series
     * @param array $item
     *
     * @return array
     */
    protected function resetSeries(array $series, array $item): array
    {
        if ($this->isMobile() || count($item) > $this->getMaxBarFixedWidth()) {
            unset($series['barWidth']);
        }

        return $series;
    }
}