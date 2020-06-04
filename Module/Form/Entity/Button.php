<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits\Args;
use Leon\BswBundle\Module\Form\Entity\Traits\BindVariable;
use Leon\BswBundle\Module\Form\Entity\Traits\Block;
use Leon\BswBundle\Module\Form\Entity\Traits\Circle;
use Leon\BswBundle\Module\Form\Entity\Traits\Click;
use Leon\BswBundle\Module\Form\Entity\Traits\Confirm;
use Leon\BswBundle\Module\Form\Entity\Traits\Ghost;
use Leon\BswBundle\Module\Form\Entity\Traits\HtmlType;
use Leon\BswBundle\Module\Form\Entity\Traits\Icon;
use Leon\BswBundle\Module\Form\Entity\Traits\Label;
use Leon\BswBundle\Module\Form\Entity\Traits\Route;
use Leon\BswBundle\Module\Form\Entity\Traits\Scene;
use Leon\BswBundle\Module\Form\Entity\Traits\Script;
use Leon\BswBundle\Module\Form\Entity\Traits\Selector;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\Type;
use Leon\BswBundle\Module\Form\Entity\Traits\Url;
use Leon\BswBundle\Module\Form\Form;

class Button extends Form
{
    use Size;
    use Route;
    use Args;
    use Icon;
    use Scene;
    use Label;
    use Block;
    use Ghost;
    use Circle;
    use Click;
    use Url;
    use Script;
    use Selector;
    use Confirm;
    use Type;
    use HtmlType;
    use BindVariable;

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
     * Button constructor.
     *
     * @param string|null $label
     * @param string|null $route
     * @param string|null $icon
     * @param string|null $type
     */
    public function __construct(string $label = null, string $route = null, string $icon = null, string $type = null)
    {
        $this->setType($type ?? self::THEME_PRIMARY);
        $this->setHtmlType(self::TYPE_BUTTON);

        isset($label) && $this->setLabel($label);
        isset($route) && $this->setRoute($route);
        isset($icon) && $this->setIcon($icon);
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

        return Helper::jsonStringify(array_merge($data, $args));
    }
}