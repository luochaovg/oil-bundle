<?php

namespace Leon\BswBundle\Module\Bsw\Menu\Entity;

use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Exception\ModuleException;

class Menu
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $menuId;

    /**
     * @var string
     */
    protected $routeName;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $javascript;

    /**
     * @var string
     */
    protected $jsonParams;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getMenuId(): int
    {
        return $this->menuId;
    }

    /**
     * @param int $menuId
     *
     * @return $this
     */
    public function setMenuId(int $menuId)
    {
        $this->menuId = $menuId;

        return $this;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     *
     * @return $this
     */
    public function setRouteName(string $routeName)
    {
        $this->routeName = $routeName;

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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue(string $value)
    {
        $this->value = $value;

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
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getJavascript(): ?string
    {
        return $this->javascript;
    }

    /**
     * @param string $javascript
     *
     * @return $this
     */
    public function setJavascript(string $javascript)
    {
        $this->javascript = $javascript;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsonParams(): ?string
    {
        return $this->jsonParams;
    }

    /**
     * @param string $jsonParams
     *
     * @return $this
     */
    public function setJsonParams(string $jsonParams)
    {
        $this->jsonParams = $jsonParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
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
     * @return string
     */
    public function getDataStringify(): string
    {
        $data = array_merge(['location' => $this->getUrl()], $this->getArgs());

        return Html::paramsBuilder($data);
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     *
     * @throws
     */
    public function attributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            if (!property_exists($this, $name)) {
                throw new ModuleException(static::class . " has no property named `{$name}`");
            }
            $this->{$name} = $value;
        }
    }
}