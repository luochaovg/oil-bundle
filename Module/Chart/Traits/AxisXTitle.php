<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait AxisXTitle
{
    /**
     * @var array
     */
    protected $axisXTitle = [];

    /**
     * @return array
     */
    public function getAxisXTitle(): array
    {
        return $this->axisXTitle;
    }

    /**
     * @param array $axisXTitle
     *
     * @return $this
     */
    public function setAxisXTitle(array $axisXTitle)
    {
        $this->axisXTitle = $axisXTitle;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAxisXTitleField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->axisXTitle[$field]);
        } else {
            $this->axisXTitle[$field] = $value;
        }

        return $this;
    }
}