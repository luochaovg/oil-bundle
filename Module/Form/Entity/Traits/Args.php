<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait Args
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getArgs(bool $meta = false): array
    {
        return $meta ? $this->args : Helper::urlEncodeValues($this->args);
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getArgsItem(string $key)
    {
        return $this->getArgs()[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getArgsString(): string
    {
        return Html::paramsBuilder($this->getArgs());
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