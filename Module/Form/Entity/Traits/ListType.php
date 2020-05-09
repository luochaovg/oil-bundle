<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ListType
{
    /**
     * @var string
     */
    protected $listType;

    /**
     * @return string
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * @param string $listType
     *
     * @return $this
     */
    public function setListType(string $listType)
    {
        $this->listType = $listType;

        return $this;
    }
}