<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Suffix
{
    /**
     * @var string
     */
    protected $suffix;

    /**
     * @return string
     */
    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setSuffix(string $suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }
}