<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Field
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @return string
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}