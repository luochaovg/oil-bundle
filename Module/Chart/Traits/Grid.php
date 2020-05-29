<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Grid
{
    /**
     * @var array
     */
    protected $grid = [];

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
        if (is_null($value)) {
            unset($this->grid[$field]);
        } else {
            $this->grid[$field] = $value;
        }

        return $this;
    }
}