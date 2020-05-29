<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait MapColor
{
    /**
     * @var array
     */
    protected $mapColor = ['white', '#009688'];

    /**
     * @return array
     */
    public function getMapColor(): array
    {
        return $this->mapColor;
    }

    /**
     * @param array $mapColor
     *
     * @return $this
     */
    public function setMapColor(array $mapColor)
    {
        $this->mapColor = $mapColor;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return $this
     */
    public function setMapColorField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->mapColor[$field]);
        } else {
            $this->mapColor[$field] = $value;
        }

        return $this;
    }
}