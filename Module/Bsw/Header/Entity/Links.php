<?php

namespace Leon\BswBundle\Module\Bsw\Header\Entity;

class Links
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
    private $route;

    /**
     * @var bool
     */
    private $script = false;

    /**
     * @var string
     */
    private $url;

    /**
     * Links constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $route
     * @param bool   $script
     */
    public function __construct(string $label = null, string $icon = null, string $route = null, bool $script = null)
    {
        isset($label) && $this->label = $label;
        isset($icon) && $this->icon = $icon;
        isset($route) && $this->route = $route;
        isset($script) && $this->script = $script;
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

    /**
     * @return bool
     */
    public function isScript(): bool
    {
        return $this->script;
    }

    /**
     * @param bool $script
     *
     * @return $this
     */
    public function setScript(bool $script = true)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }
}