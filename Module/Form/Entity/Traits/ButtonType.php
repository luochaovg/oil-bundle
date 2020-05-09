<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ButtonType
{
    /**
     * @var string
     */
    protected $buttonType;

    /**
     * @return string
     */
    public function getButtonType(): string
    {
        return $this->buttonType;
    }

    /**
     * @param string $buttonType
     *
     * @return $this
     */
    public function setButtonType(string $buttonType)
    {
        $this->buttonType = $buttonType;

        return $this;
    }
}