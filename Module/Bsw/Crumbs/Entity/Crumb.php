<?php

namespace Leon\BswBundle\Module\Bsw\Crumbs\Entity;

class Crumb
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $route;

    /**
     * Crumbs constructor.
     *
     * @param string $label
     * @param string $route
     * @param string $icon
     */
    public function __construct(string $label = null, string $route = null, string $icon = null)
    {
        isset($label) && $this->label = $label;
        isset($route) && $this->route = $route;
        isset($icon) && $this->icon = $icon;
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
    public function getIcon(): ?string
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
     * @param string $route
     *
     * @return string
     */
    public function getRoute(string $route = ''): string
    {
        return $this->route ?? $route;
    }

    /**
     * @param string $route
     *
     * @return $this
     */
    public function setRoute(string $route)
    {
        $this->route = $route;

        return $this;
    }
}