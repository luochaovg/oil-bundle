<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Value
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return str_replace(['`'], ['\`'], $this->value);
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}