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
     * @var bool
     */
    private $html = false;

    /**
     * Charm constructor.
     *
     * @param string $charm
     * @param null   $value
     * @param bool   $html
     */
    public function __construct(string $charm = null, $value = null, bool $html = null)
    {
        isset($charm) && $this->charm = $charm;
        isset($value) && $this->value = $value;
        isset($html) && $this->html = $html;
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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

    /**
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->html;
    }

    /**
     * @param bool $html
     *
     * @return $this
     */
    public function setHtml(bool $html)
    {
        $this->html = $html;

        return $this;
    }
}