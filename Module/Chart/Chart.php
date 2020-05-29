<?php

namespace Leon\BswBundle\Module\Chart;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Chart\Traits\AxisX;
use Leon\BswBundle\Module\Chart\Traits\AxisXTitle;
use Leon\BswBundle\Module\Chart\Traits\AxisY;
use Leon\BswBundle\Module\Chart\Traits\DataField;
use Leon\BswBundle\Module\Chart\Traits\DataList;
use Leon\BswBundle\Module\Chart\Traits\Grid;
use Leon\BswBundle\Module\Chart\Traits\Legend;
use Leon\BswBundle\Module\Chart\Traits\LegendTitle;
use Leon\BswBundle\Module\Chart\Traits\MaxZoom;
use Leon\BswBundle\Module\Chart\Traits\Mobile;
use Leon\BswBundle\Module\Chart\Traits\OptionExtra;
use Leon\BswBundle\Module\Chart\Traits\SaveName;
use Leon\BswBundle\Module\Chart\Traits\Selected;
use Leon\BswBundle\Module\Chart\Traits\SelectedMode;
use Leon\BswBundle\Module\Chart\Traits\Series;
use Leon\BswBundle\Module\Chart\Traits\SeriesExtra;
use Leon\BswBundle\Module\Chart\Traits\SubTitle;
use Leon\BswBundle\Module\Chart\Traits\SubTitleLink;
use Leon\BswBundle\Module\Chart\Traits\Title;
use Leon\BswBundle\Module\Chart\Traits\Toolbox;
use Leon\BswBundle\Module\Chart\Traits\Tooltip;
use Leon\BswBundle\Module\Chart\Traits\TooltipFormat;
use Leon\BswBundle\Module\Chart\Traits\Type;

abstract class Chart
{
    use Mobile;
    use Type;
    use Title;
    use SubTitle;
    use SubTitleLink;
    use Tooltip;
    use TooltipFormat;
    use Toolbox;
    use SaveName;
    use Legend;
    use LegendTitle;
    use SelectedMode;
    use Selected;
    use Grid;
    use DataField;
    use DataList;
    use AxisX;
    use AxisXTitle;
    use AxisY;
    use MaxZoom;
    use Series;
    use SeriesExtra;
    use OptionExtra;

    /**
     * Chart constructor.
     *
     * @param array $options
     * @param bool  $mobile
     */
    public function __construct(array $options, bool $mobile)
    {
        $this->setMobile($mobile);

        $this->setClass(static::class);
        $type = strtolower(Helper::clsName($this->getClass()));

        foreach ($options as $key => $value) {
            $key = Helper::underToCamel($key, false);
            $fn = "set{$key}";
            if (method_exists($this, $fn)) {
                $this->{$fn}($value);
            }
        }

        if ($feature = $this->getFeature()) {
            $feature['saveAsImage']['name'] = $this->getTitle() ?: ($this->getSubTitle() ?: $this->getSaveName());
            $this->setFeature($feature);
        }

        $this->init();

        $series = [];
        $seriesExtra = $this->getSeriesExtra();
        foreach ($this->getData() as $name => $item) {

            $buildSeries = $this->buildSeries($name, $item);
            $defaultSeries = [
                'name' => $name,
                'data' => $item,
                'type' => $type,
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
     * Get the latest options
     *
     * @return array
     */
    public function option(): array
    {
        $options = [];

        // title
        if (($title = $this->getTitle()) !== false) {
            $options['title'] = [
                'text'      => $title,
                'textStyle' => [
                    'fontSize'   => 14,
                    'fontWeight' => 'lighter',
                ],
                'subtext'   => $this->getSubTitle(),
                'sublink'   => $this->getSubTitleLink(),
                'x'         => 'center',
                'itemGap'   => 8,
                'bottom'    => ($title && $this->getSubTitle()) ? 0 : 8,
            ];
        }

        // tooltip
        if (($tooltip = $this->getTooltip()) !== false) {
            $options['tooltip'] = $tooltip;
        }

        // toolbox
        if (($toolbox = $this->getToolbox()) !== false) {
            $options['toolbox'] = [
                'show'     => true,
                'orient'   => 'vertical',
                'top'      => 50,
                'right'    => $this->isMobile() ? 0 : 20,
                'itemSize' => 10,
                'feature'  => $this->getFeature(),
            ];
        }

        // legend
        if (($legend = $this->getLegend()) !== false) {
            $options['legend'] = array_merge(
                [
                    'data'         => $this->getLegendTitle(),
                    'selectedMode' => $this->getSelectedMode(),
                    'selected'     => $this->getSelected(),
                    'type'         => 'scroll',
                    'align'        => 'auto',
                    'top'          => 8,
                    'width'        => '90%',
                ],
                is_array($legend) ? $legend : []
            );
        }

        // grid
        if (($grid = $this->getGrid()) !== false) {
            $options['grid'] = [
                'top'          => 50,
                'right'        => $this->isMobile() ? 0 : 50,
                'bottom'       => 50,
                'left'         => $this->isMobile() ? 0 : 40,
                'containLabel' => true,
            ];
        }

        // x-axis
        if ($this->getXAxis() !== false) {
            $this->setXAxisField('data', $this->getXAxisTitle());
            $options['xAxis'] = $this->getXAxis();
        }

        // y-axis
        if (($yAxis = $this->getYAxis()) !== false) {
            $options['yAxis'] = $yAxis;
        }

        // zoom
        $totalXAxis = $total = count($this->getField());
        $maxZoom = $this->getMaxZoom();
        if ($maxZoom && $totalXAxis > $maxZoom) {

            $percent = intval(($maxZoom / $totalXAxis) * 100);
            $percent = $percent > 100 ? 100 : $percent;

            $options['dataZoom'] = [
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

        // series
        $options['series'] = $this->getSeries();
        $options = Helper::merge($this->buildOptions($options), $this->getOptionExtra());

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
     * @param array  $item
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