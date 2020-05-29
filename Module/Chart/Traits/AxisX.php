<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait AxisX
{
    /**
     * @var array
     */
    protected $axisX = [];

    /**
     * @return array
     */
    public function getAxisX()
    {
        return $this->axisX;
    }

    /**
     * @param array $axisX
     *
     * @return $this
     */
    public function setAxisX(array $axisX)
    {
        $this->axisX = $axisX;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAxisXField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->axisX[$field]);
        } else {
            $this->axisX[$field] = $value;
        }

        return $this;
    }
}