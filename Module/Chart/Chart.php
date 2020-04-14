<?php

namespace Leon\BswBundle\Module\Chart;

use Leon\BswBundle\Component\Helper;

abstract class Chart
{
    /**
     * @var bool
     */
    protected $mobile = false;

    /**
     * @var bool
     */
    protected $popup = false;

    /**
     * @var string
     */
    protected $chartCls;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $subTitle;

    /**
     * @var string
     */
    protected $subTitleLink;

    /**
     * @var array
     */
    protected $tooltip = [];

    /**
     * @var string
     */
    protected $tooltipFmt;

    /**
     * @var array
     */
    protected $toolbox = [];

    /**
     * @var array
     */
    protected $feature = [
        'saveAsImage' => [
            'title'      => 'Save',
            'pixelRatio' => 2,
        ],
    ];

    /**
     * @var string
     */
    protected $saveName;

    /**
     * @var array
     */
    protected $legend = [];

    /**
     * @var array
     */
    protected $legendTitle = [];

    /**
     * @var string
     */
    protected $selectedMode = 'multiple';

    /**
     * @var array
     */
    protected $selected = [];

    /**
     * @var array
     */
    protected $grid = [];

    /**
     * @var array
     */
    protected $dataField = [];

    /**
     * @var array
     */
    protected $dataList = [];

    /**
     * @var array
     */
    protected $xAxis = [];

    /**
     * @var array
     */
    protected $xAxisTitle = [];

    /**
     * @var array
     */
    protected $yAxis = [];

    /**
     * @var int
     */
    protected $maxZoom = 50;

    /**
     * @var array
     */
    protected $series = [];

    /**
     * @var array
     */
    protected $seriesExtra = [];

    /**
     * @var array
     */
    protected $optionExtra = [];

    /**
     * @return bool
     */
    public function isMobile(): bool
    {
        return $this->mobile;
    }

    /**
     * @param bool $mobile
     *
     * @return $this
     */
    public function setMobile(bool $mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPopup(): bool
    {
        return $this->popup;
    }

    /**
     * @param bool $popup
     *
     * @return $this
     */
    public function setPopup(bool $popup)
    {
        $this->popup = $popup;

        return $this;
    }

    /**
     * @return string
     */
    public function getChartCls(): string
    {
        return $this->chartCls;
    }

    /**
     * @param string $chartCls
     *
     * @return $this
     */
    public function setChartCls(string $chartCls)
    {
        $this->chartCls = $chartCls;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    /**
     * @param string $subTitle
     *
     * @return $this
     */
    public function setSubTitle(string $subTitle)
    {
        $this->subTitle = $subTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubTitleLink(): ?string
    {
        return $this->subTitleLink;
    }

    /**
     * @param string $subTitleLink
     *
     * @return $this
     */
    public function setSubTitleLink(string $subTitleLink)
    {
        $this->subTitleLink = $subTitleLink;

        return $this;
    }

    /**
     * @return array
     */
    public function getTooltip(): array
    {
        return $this->tooltip;
    }

    /**
     * @param array $tooltip
     *
     * @return $this
     */
    public function setTooltip(array $tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setTooltipField(string $field, $value)
    {
        $this->tooltip[$field] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTooltipFmt(): ?string
    {
        return $this->tooltipFmt;
    }

    /**
     * @param string $tooltipFmt
     *
     * @return $this
     */
    public function setTooltipFmt(string $tooltipFmt)
    {
        $this->tooltipFmt = $tooltipFmt;

        return $this;
    }

    /**
     * @return array
     */
    public function getToolbox(): array
    {
        return $this->toolbox;
    }

    /**
     * @param array $toolbox
     *
     * @return $this
     */
    public function setToolbox(array $toolbox)
    {
        $this->toolbox = $toolbox;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setToolboxField(string $field, $value)
    {
        $this->toolbox[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getFeature(): array
    {
        return $this->feature;
    }

    /**
     * @param array $feature
     *
     * @return $this
     */
    public function setFeature(array $feature)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setFeatureField(string $field, $value)
    {
        $this->feature[$field] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaveName(): ?string
    {
        return $this->saveName;
    }

    /**
     * @param string $saveName
     *
     * @return $this
     */
    public function setSaveName(string $saveName)
    {
        $this->saveName = $saveName;

        return $this;
    }

    /**
     * @return array
     */
    public function getLegend(): array
    {
        return $this->legend;
    }

    /**
     * @param array $legend
     *
     * @return $this
     */
    public function setLegend(array $legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setLegendField(string $field, $value)
    {
        $this->legend[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getLegendTitle(): array
    {
        return $this->legendTitle;
    }

    /**
     * @param array $legendTitle
     *
     * @return $this
     */
    public function setLegendTitle(array $legendTitle)
    {
        $this->legendTitle = $legendTitle;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setLegendTitleField(string $field, $value)
    {
        $this->legendTitle[$field] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedMode(): string
    {
        return $this->selectedMode;
    }

    /**
     * @param string $selectedMode
     *
     * @return $this
     */
    public function setSelectedMode(string $selectedMode)
    {
        $this->selectedMode = $selectedMode;

        return $this;
    }

    /**
     * @return array
     */
    public function getSelected(): array
    {
        return $this->selected;
    }

    /**
     * @param array $selected
     *
     * @return $this
     */
    public function setSelected(array $selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSelectedField(string $field, $value)
    {
        $this->selected[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * @param array $grid
     *
     * @return $this
     */
    public function setGrid(array $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setGridField(string $field, $value)
    {
        $this->grid[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataField(): array
    {
        return $this->dataField;
    }

    /**
     * @param array $dataField
     *
     * @return $this
     */
    public function setDataField(array $dataField)
    {
        $this->dataField = $dataField;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setDataFieldField(string $field, $value)
    {
        $this->dataField[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataList(): array
    {
        return $this->dataList;
    }

    /**
     * @param array $dataList
     *
     * @return $this
     */
    public function setDataList(array $dataList)
    {
        $this->dataList = $dataList;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setDataListField(string $field, $value)
    {
        $this->dataList[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getXAxis(): array
    {
        return $this->xAxis;
    }

    /**
     * @param array $xAxis
     *
     * @return $this
     */
    public function setXAxis(array $xAxis)
    {
        $this->xAxis = $xAxis;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setXAxisField(string $field, $value)
    {
        $this->xAxis[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getXAxisTitle(): array
    {
        return $this->xAxisTitle;
    }

    /**
     * @param array $xAxisTitle
     *
     * @return $this
     */
    public function setXAxisTitle(array $xAxisTitle)
    {
        $this->xAxisTitle = $xAxisTitle;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setXAxisTitleField(string $field, $value)
    {
        $this->xAxisTitle[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getYAxis(): array
    {
        return $this->yAxis;
    }

    /**
     * @param array $yAxis
     *
     * @return $this
     */
    public function setYAxis(array $yAxis)
    {
        $this->yAxis = $yAxis;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setYAxisField(string $field, $value)
    {
        $this->yAxis[$field] = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxZoom(): int
    {
        return $this->maxZoom;
    }

    /**
     * @param int $maxZoom
     *
     * @return $this
     */
    public function setMaxZoom(int $maxZoom)
    {
        $this->maxZoom = $maxZoom;

        return $this;
    }

    /**
     * @return array
     */
    public function getSeries(): array
    {
        return $this->series;
    }

    /**
     * @param array $series
     *
     * @return $this
     */
    public function setSeries(array $series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSeriesField(string $field, $value)
    {
        $this->series[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getSeriesExtra(): array
    {
        return $this->seriesExtra;
    }

    /**
     * @param array $seriesExtra
     *
     * @return $this
     */
    public function setSeriesExtra(array $seriesExtra)
    {
        $this->seriesExtra = $seriesExtra;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSeriesExtraField(string $field, $value)
    {
        $this->seriesExtra[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptionExtra(): array
    {
        return $this->optionExtra;
    }

    /**
     * @param array $optionExtra
     *
     * @return $this
     */
    public function setOptionExtra(array $optionExtra)
    {
        $this->optionExtra = $optionExtra;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOptionExtraField(string $field, $value)
    {
        $this->optionExtra[$field] = $value;

        return $this;
    }

    /**
     * Chart constructor.
     *
     * @param array $options
     * @param bool  $mobile
     * @param bool  $popup
     */
    public function __construct(array $options, bool $mobile, bool $popup = false)
    {
        $this->setMobile($mobile);
        $this->setPopup($popup);

        $this->setChartCls(static::class);
        $type = strtolower(Helper::clsName($this->getChartCls()));

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
        foreach ($this->getDataList() as $name => $item) {

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
        $totalXAxis = $total = count($this->getDataField());
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