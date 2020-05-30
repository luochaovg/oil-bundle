<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Component\Helper;

trait Tooltip
{
    /**
     * @var array
     */
    protected $tooltip = [];

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
        Helper::setArrayValue($this->tooltip, $field, $value);

        return $this;
    }
}