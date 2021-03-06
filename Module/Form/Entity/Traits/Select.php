<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Select
{
    /**
     * @var string
     */
    protected $select;

    /**
     * @return string
     */
    public function getSelect(): ?string
    {
        return $this->select;
    }

    /**
     * @param string $select
     *
     * @return $this
     */
    public function setSelect(string $select = null)
    {
        $this->select = $select;

        return $this;
    }
}