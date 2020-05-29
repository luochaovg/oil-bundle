<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait TooltipFormat
{
    /**
     * @var string
     */
    protected $tooltipFormat;

    /**
     * @return string
     */
    public function getTooltipFormat(): ?string
    {
        return $this->tooltipFormat;
    }

    /**
     * @param string $tooltipFormat
     *
     * @return $this
     */
    public function setTooltipFormat(string $tooltipFormat)
    {
        $this->tooltipFormat = $tooltipFormat;

        return $this;
    }
}