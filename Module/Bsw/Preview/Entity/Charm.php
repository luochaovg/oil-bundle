<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Entity;

class Charm
{
    /**
     * @var string
     */
    private $charm;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Charm constructor.
     *
     * @param string|null $charm
     * @param null        $value
     */
    public function __construct(string $charm = null, $value = null)
    {
        isset($charm) && $this->charm = $charm;
        isset($value) && $this->value = $value;
    }

    /**
     * @return string
     */
    public function getCharm(): string
    {
        return $this->charm ?? '';
    }

    /**
     * @param string $charm
     *
     * @return $this
     */
    public function setCharm(string $charm)
    {
        $this->charm = $charm;

        return $this;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getValue($default = null)
    {
        return $this->value ?? $default;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}