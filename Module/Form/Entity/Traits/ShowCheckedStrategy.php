<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ShowCheckedStrategy
{
    /**
     * @var string
     */
    protected $showCheckedStrategy;

    /**
     * @return string
     */
    public function getShowCheckedStrategy(): string
    {
        return $this->showCheckedStrategy;
    }

    /**
     * @param string $showCheckedStrategy
     *
     * @return $this
     */
    public function setShowCheckedStrategy(string $showCheckedStrategy)
    {
        $this->showCheckedStrategy = $showCheckedStrategy;

        return $this;
    }
}