<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Radius
{
    /**
     * @var array
     */
    protected $radius = [];

    /**
     * @return array
     */
    public function getRadius(): array
    {
        return $this->radius;
    }

    /**
     * @param array $radius
     *
     * @return $this
     */
    public function setRadius(array $radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return $this
     */
    public function setRadiusField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->radius[$field]);
        } else {
            $this->radius[$field] = $value;
        }

        return $this;
    }
}