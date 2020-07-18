<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait VarNameForKey
{
    /**
     * @var string
     */
    protected $varNameForKey;

    /**
     * @return string
     */
    public function getVarNameForKey(): string
    {
        return $this->varNameForKey;
    }

    /**
     * @param string $varNameForKey
     *
     * @return $this
     */
    public function setVarNameForKey(string $varNameForKey)
    {
        $this->varNameForKey = $varNameForKey;

        return $this;
    }
}