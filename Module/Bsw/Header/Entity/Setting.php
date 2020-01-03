<?php

namespace Leon\BswBundle\Module\Bsw\Header\Entity;

class Setting
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $click;

    /**
     * Setting constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $click
     */
    public function __construct(string $label = null, string $icon = null, string $click = null)
    {
        isset($label) && $this->label = $label;
        isset($icon) && $this->icon = $icon;
        isset($click) && $this->click = $click;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getClick(): string
    {
        return $this->click;
    }

    /**
     * @param string $click
     *
     * @return $this
     */
    public function setClick(string $click)
    {
        $this->click = $click;

        return $this;
    }
}