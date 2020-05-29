<?php

namespace Leon\BswBundle\Module\Chart\Traits;

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
        if (is_null($value)) {
            unset($this->tooltip[$field]);
        } else {
            $this->tooltip[$field] = $value;
        }

        return $this;
    }
}