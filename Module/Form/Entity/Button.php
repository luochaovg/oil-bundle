<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\Args;
use Leon\BswBundle\Module\Form\Entity\Traits\Icon;
use Leon\BswBundle\Module\Form\Entity\Traits\Route;
use Leon\BswBundle\Module\Form\Entity\Traits\Scene;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Button extends Form
{
    use Size;
    use Route;
    use Args;
    use Icon;
    use Scene;

    /**
     * @const string
     */
    const THEME_PRIMARY       = 'primary';
    const THEME_DASHED        = 'dashed';
    const THEME_DANGER        = 'danger';
    const THEME_LINK          = 'link';
    const THEME_DEFAULT       = 'default';
    const THEME_BSW_PRIMARY   = 'bsw-primary bsw-btn';
    const THEME_BSW_SECONDARY = 'bsw-secondary bsw-btn';
    const THEME_BSW_SUCCESS   = 'bsw-success bsw-btn';
    const THEME_BSW_DANGER    = 'bsw-danger bsw-btn';
    const THEME_BSW_WARNING   = 'bsw-warning bsw-btn';
    const THEME_BSW_INFO      = 'bsw-info bsw-btn';
    const THEME_BSW_LIGHT     = 'bsw-light bsw-btn';
    const THEME_BSW_DARK      = 'bsw-dark bsw-btn';

    /**
     * @const string
     * @see   https://developer.mozilla.org/zh-CN/docs/Web/HTML/Element/button#Form_%3Cbutton%3E_types
     */
    const TYPE_SUBMIT = 'submit';
    const TYPE_RESET  = 'reset';
    const TYPE_BUTTON = 'button';

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $type = self::THEME_PRIMARY;

    /**
     * @var bool
     */
    protected $block = false;

    /**
     * @var bool
     */
    protected $ghost = false;

    /**
     * @var string
     */
    protected $htmlType = self::TYPE_BUTTON;

    /**
     * @var bool
     */
    protected $circle = false;

    /**
     * @var string
     */
    protected $click = 'redirect';

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $script;

    /**
     * @var string
     */
    protected $selector;

    /**
     * @var string
     */
    protected $confirm;

    /**
     * Button constructor.
     *
     * @param string|null $label
     * @param string|null $route
     * @param string|null $icon
     * @param string|null $type
     */
    public function __construct(string $label = null, string $route = null, string $icon = null, string $type = null)
    {
        isset($label) && $this->label = $label;
        isset($route) && $this->route = $route;
        isset($icon) && $this->icon = $icon;
        isset($type) && $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel(): ?string
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBlock(): bool
    {
        return $this->block;
    }

    /**
     * @param bool $block
     *
     * @return $this
     */
    public function setBlock(bool $block = true)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGhost(): bool
    {
        return $this->ghost;
    }

    /**
     * @param bool $ghost
     *
     * @return $this
     */
    public function setGhost(bool $ghost = true)
    {
        $this->ghost = $ghost;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlType(): string
    {
        return $this->htmlType;
    }

    /**
     * @param string $htmlType
     *
     * @return $this
     */
    public function setHtmlType(string $htmlType)
    {
        $this->htmlType = $htmlType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCircle(): bool
    {
        return $this->circle;
    }

    /**
     * @param bool $circle
     *
     * @return $this
     */
    public function setCircle(bool $circle = true)
    {
        $this->label = null;
        $this->circle = $circle;

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

    /**
     * @return string
     */
    public function getSelector(): ?string
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function setSelector(string $selector)
    {
        if (in_array($selector, [Abs::SELECTOR_CHECKBOX, Abs::SELECTOR_RADIO])) {
            $this->selector = $selector;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url ?? '';
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

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script ?? '';
    }

    /**
     * @param string $script
     *
     * @return $this
     */
    public function setScript(string $script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    /**
     * @param string $confirm
     *
     * @return $this
     */
    public function setConfirm(?string $confirm = null)
    {
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        $data = [
            'route'    => $this->getRoute(),
            'location' => $this->getUrl(),
            'function' => $this->getClick(),
        ];

        $args = $this->getArgs();
        if ($confirm = $this->getConfirm()) {
            $args['confirm'] = $confirm;
        }

        return json_encode(array_merge($data, $args), JSON_UNESCAPED_UNICODE);
    }
}