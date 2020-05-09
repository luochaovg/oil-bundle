<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Circle
{
    /**
     * @var bool
     */
    protected $circle = false;

    /**
     * @return bool
     */
    public function isCircle(): bool
    {
        return $this->circle;
    }

    /**
     * @param bool $circle
     *
     * @return $this
     */
    public function setCircle(bool $circle = true)
    {
        $this->circle = $circle;

        return $this;
    }
}