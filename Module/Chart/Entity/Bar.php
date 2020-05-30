<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Traits;

class Bar extends Line
{
    use Traits\LabelStackTpl,
        Traits\MaxBarFixedWidth;

    /**
     * @return string
     */
    protected function type(): string
    {
        return 'bar';
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
    protected function rebuildSeries(array $series, array $item): array
    {
        if ($this->isMobile() || count($item) > $this->getMaxBarFixedWidth()) {
            unset($series['barWidth']);
        }

        return $series;
    }
}