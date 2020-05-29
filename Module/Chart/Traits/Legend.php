<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Legend
{
    /**
     * @var array
     */
    protected $legend = [];

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
        if (is_null($value)) {
            unset($this->legend[$field]);
        } else {
            $this->legend[$field] = $value;
        }

        return $this;
    }
}