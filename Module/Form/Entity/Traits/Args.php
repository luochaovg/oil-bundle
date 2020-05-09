<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Html;

trait Args
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return string
     */
    public function getArgsString(): string
    {
        return Html::paramsBuilder($this->args);
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function appendArgs(array $args)
    {
        $this->args = array_merge($this->args, $args);

        return $this;
    }
}