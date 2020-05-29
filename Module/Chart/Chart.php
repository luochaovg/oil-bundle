<?php

namespace Leon\BswBundle\Module\Chart;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Chart\Traits;

abstract class Chart
{
    use Traits\Module,
        Traits\Width,
        Traits\Height,
        Traits\Style,
        Traits\Theme,
        Traits\Api,
        Traits\AxisX,
        Traits\AxisXTitle,
        Traits\AxisY,
        Traits\DataField,
        Traits\DataList,
        Traits\Feature,
        Traits\Grid,
        Traits\Legend,
        Traits\LegendTitle,
        Traits\MaxZoom,
        Traits\Mobile,
        Traits\OptionExtra,
        Traits\SaveName,
        Traits\Selected,
        Traits\SelectedMode,
        Traits\Series,
        Traits\SeriesExtra,
        Traits\SubTitle,
        Traits\SubTitleLink,
        Traits\Title,
        Traits\Toolbox,
        Traits\Tooltip,
        Traits\TooltipTpl,
        Traits\Type;

    /**
     * Chart constructor.
     */
    public function __construct()
    {
        $this->setMobile(Helper::isMobile());
        $this->setType(Helper::clsName(static::class));

        if ($feature = $this->getFeature()) {
            $this->setFeatureField('saveAsImage.name', $this->getSaveName() ?: $this->getTitle());
        }

        $this->init();

        $series = [];
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

            array_push($series, $this->resetSeries($buildSeries, $item));
        }

        $this->setSeries($series);
    }

    /**
     * @return string
     */
    public function option(): string
    {
        $options = [];

        // title
        if ($this->moduleState('title')) {
            $options['title'] = [
                'text' => $this->getTitle(),
                'textStyle' => [
                    'fontSize' => 14,
                    'fontWeight' => 'lighter',
                ],
                'subtext' => $this->getSubTitle(),
                'sublink' => $this->getSubTitleLink(),
                'x' => 'center',
                'itemGap' => 8,
                'bottom' => ($this->getTitle() && $this->getSubTitle()) ? 0 : 8,
            ];
        }

        // tooltip
        if ($this->moduleState('tooltip')) {
            $options['tooltip'] = $this->getTooltip();
        }

        // toolbox
        if ($this->moduleState('toolbox')) {
            $options['toolbox'] = Helper::merge([
                'show' => true,
                'orient' => 'vertical',
                'top' => 50,
                'right' => $this->isMobile() ? 0 : 20,
                'itemSize' => 10,
                'feature' => $this->getFeature(),
            ], $this->getToolbox());
        }

        // legend
        if ($this->moduleState('legend')) {
            $options['legend'] = Helper::merge(
                [
                    'data' => $this->getLegendTitle(),
                    'selectedMode' => $this->getSelectedMode(),
                    'selected' => $this->getSelected(),
                    'type' => 'scroll',
                    'align' => 'auto',
                    'top' => 8,
                    'width' => '90%',
                ],
                $this->getLegend()
            );
        }

        // grid
        if ($this->moduleState('grid')) {
            $options['grid'] = Helper::merge([
                'top' => 50,
                'right' => $this->isMobile() ? 0 : 50,
                'bottom' => 50,
                'left' => $this->isMobile() ? 0 : 40,
                'containLabel' => true,
            ], $this->getGrid());
        }

        // x-axis
        if ($this->moduleState('axisX')) {
            $this->setAxisXField('data', $this->getAxisXTitle());
            $options['xAxis'] = $this->getAxisX();
        }

        // y-axis
        if ($this->moduleState('axisY')) {
            $options['yAxis'] = $this->getAxisY();
        }

        // zoom
        if ($this->moduleState('zoom')) {

            $maxZoom = $this->getMaxZoom();
            $totalXAxis = count($this->getDataField());

            if ($maxZoom && ($totalXAxis > $maxZoom)) {
                $percent = intval(($maxZoom / $totalXAxis) * 100);
                $percent = $percent > 100 ? 100 : $percent;
                $options['dataZoom'] = [
                    [
                        'type' => 'inside',
                        'start' => 100 - $percent,
                        'end' => 100,
                        'zoomLock' => true,
                    ],
                    [
                        'type' => 'slider',
                        'start' => 100 - $percent,
                        'end' => 100,
                    ],
                ];
            }
        }

        // series
        if ($this->moduleState('series')) {
            $options['series'] = $this->getSeries();
        }

        $options = Helper::merge($this->buildOptions($options), $this->getOptionExtra());
        $options = Helper::jsonStringify($options, '{}');

        return $options;
    }

    /**
     * Init like option with diff type
     *
     * @return mixed
     */
    abstract protected function init();

    /**
     * Build series
     *
     * @param string $name
     * @param array $item
     *
     * @return array
     */
    abstract protected function buildSeries(string $name, array $item): array;

    /**
     * Reset series
     *
     * @param array $series
     * @param array $item
     *
     * @return array
     */
    protected function resetSeries(array $series, array $item): array
    {
        return $series;
    }

    /**
     * Build options
     *
     * @param array $options
     *
     * @return array
     */
    protected function buildOptions(array $options): array
    {
        return $options;
    }
}