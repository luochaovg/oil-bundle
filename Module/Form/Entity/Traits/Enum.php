<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Enum
{
    /**
     * @var array
     */
    protected $enum = [];

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @param array $enum
     *
     * @return $this
     */
    public function setEnum(array $enum)
    {
        $this->enum = $enum;

        return $this;
    }
}