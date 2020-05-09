<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Mode
{
    /**
     * @var string
     */
    protected $mode;

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    public function setMode(string $mode)
    {
        $this->mode = $mode;

        return $this;
    }
}