<?php

namespace Leon\BswBundle\Module\Chart;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Chart\Traits;

abstract class Chart
{
    use Traits\Api,
        Traits\AxisX,
        Traits\AxisXTitle,
        Traits\AxisY,
        Traits\DataField,
        Traits\DataList,
        Traits\Feature,
        Traits\Grid,
        Traits\Height,
        Traits\Legend,
        Traits\LegendTitle,
        Traits\MaxZoom,
        Traits\Mobile,
        Traits\Module,
        Traits\Name,
        Traits\Option,
        Traits\OptionExtra,
        Traits\SaveName,
        Traits\Selected,
        Traits\SelectedMode,
        Traits\Series,
        Traits\SeriesExtra,
        Traits\Style,
        Traits\SubTitle,
        Traits\SubTitleLink,
        Traits\Theme,
        Traits\Title,
        Traits\Toolbox,
        Traits\Tooltip,
        Traits\TooltipTpl,
        Traits\Type,
        Traits\Width;

    /**
     * @const string
     */
    const SELECTED_MODE_MULTIPLE = 'multiple';
    const SELECTED_MODE_SINGLE   = 'single';

    /**
     * Chart constructor.
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->setMobile(Helper::isMobile());
        $this->setType($this->type());
        $this->setSelectedMode(self::SELECTED_MODE_MULTIPLE);

        if ($name) {
            $this->setName($name);
        }
    }

    /**
     * Type
     *
     * @return string
     */
    abstract protected function type(): string;

    /**
     * Init
     *
     * @return mixed
     */
    abstract protected function init();

    /**
     * Build option
     *
     * @return $this
     */
    final public function buildOption()
    {
        $this->init();

        // title
        if ($this->moduleState('title')) {
            $option['title'] = [
                'text'      => $this->getTitle(),
                'textStyle' => [
                    'fontSize'   => 14,
                    'fontWeight' => 'lighter',
                ],
                'subtext'   => $this->getSubTitle(),
                'sublink'   => $this->getSubTitleLink(),
                'x'         => 'center',
                'itemGap'   => 8,
                'bottom'    => ($this->getTitle() && $this->getSubTitle()) ? 0 : 8,
            ];
        }

        // tooltip
        if ($this->moduleState('tooltip')) {
            $option['tooltip'] = $this->getTooltip();
        }

        // toolbox
        if ($this->moduleState('toolbox')) {
            $this->setFeatureField('saveAsImage.name', $this->getSaveName() ?: $this->getTitle());
            $option['toolbox'] = Helper::merge(
                [
                    'show'     => true,
                    'orient'   => 'vertical',
                    'top'      => 20,
                    'right'    => $this->isMobile() ? 0 : 20,
                    'itemSize' => 10,
                    'feature'  => $this->getFeature(),
                ],
                $this->getToolbox()
            );
        }

        // legend
        if ($this->moduleState('legend')) {
            $option['legend'] = Helper::merge(
                [
                    'data'         => $this->getLegendTitle(),
                    'selectedMode' => $this->getSelectedMode(),
                    'selected'     => $this->getSelected(),
                    'type'         => 'scroll',
                    'align'        => 'auto',
                    'top'          => 8,
                    'width'        => '90%',
                ],
                $this->getLegend()
            );
        }

        // grid
        if ($this->moduleState('grid')) {
            if (!$this->getTitle() && !$this->getSubTitle()) {
                $this->setGridField('bottom', 0);
            }
            $option['grid'] = Helper::merge(
                [
                    'top'          => 60,
                    'right'        => $this->isMobile() ? 0 : 50,
                    'bottom'       => 45,
                    'left'         => $this->isMobile() ? 0 : 40,
                    'containLabel' => true,
                ],
                $this->getGrid()
            );
        }

        // x-axis
        if ($this->moduleState('axisX')) {
            $this->setAxisXField('data', $this->getAxisXTitle());
            $option['xAxis'] = $this->getAxisX();
        }

        // y-axis
        if ($this->moduleState('axisY')) {
            $option['yAxis'] = $this->getAxisY();
        }

        // zoom
        if ($this->moduleState('zoom')) {

            $maxZoom = $this->getMaxZoom();
            $totalXAxis = count($this->getDataField());

            if ($maxZoom && ($totalXAxis > $maxZoom)) {
                $percent = intval(($maxZoom / $totalXAxis) * 100);
                $percent = $percent > 100 ? 100 : $percent;
                $option['dataZoom'] = [
                    [
                        'type'     => 'inside',
                        'start'    => 100 - $percent,
                        'end'      => 100,
                        'zoomLock' => true,
                    ],
                    [
                        'type'  => 'slider',
                        'start' => 100 - $percent,
                        'end'   => 100,
                    ],
                ];
            }
        }

        // series
        if ($this->moduleState('series')) {
            $series = $this->getSeries();
            $seriesExtra = $this->getSeriesExtra();

            foreach ($this->getDataList() as $name => $item) {
                $buildSeries = $this->buildSeries($name, $item);
                $defaultSeries = [
                    'name' => $name,
                    'data' => $item,
                    'type' => $this->getType(),
                ];

                $buildSeries = Helper::merge($defaultSeries, $buildSeries, $seriesExtra[$name] ?? []);
                $buildSeries = array_filter(
                    $buildSeries,
                    function ($v) {
                        return !is_null($v);
                    }
                );
                array_push($series, $this->rebuildSeries($buildSeries, $item));
            }

            $option['series'] = $series;
        }

        $option = $this->rebuildOption($option ?? []);
        $option = Helper::merge($option, $this->getOptionExtra());

        return $this->setOption($option);
    }

    /**
     * Rebuild option
     *
     * @param array $option
     *
     * @return array
     */
    protected function rebuildOption(array $option): array
    {
        return $option;
    }

    /**
     * Build series
     *
     * @param string $name
     * @param array  $item
     *
     * @return array
     */
    abstract protected function buildSeries(string $name, array $item): array;

    /**
     * Rebuild series
     *
     * @param array $series
     * @param array $item
     *
     * @return array
     */
    protected function rebuildSeries(array $series, array $item): array
    {
        return $series;
    }
}