<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Selected
{
    /**
     * @var array
     */
    protected $selected = [];

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
        if (is_null($value)) {
            unset($this->selected[$field]);
        } else {
            $this->selected[$field] = $value;
        }

        return $this;
    }
}