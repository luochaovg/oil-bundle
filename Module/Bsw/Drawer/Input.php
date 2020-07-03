<?php

namespace Leon\BswBundle\Module\Bsw\Drawer;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $title = 'Drawer';

    /**
     * @var string|int
     */
    public $width = Abs::MEDIA_SM;

    /**
     * @var int
     */
    public $height = 512;

    /**
     * @var string
     */
    public $placement = Abs::POS_LEFT;

    /**
     * @var string
     */
    public $wrapClassName = null;

    /**
     * @var bool
     */
    public $keyboard = false;

    /**
     * @var bool
     */
    public $mask = true;

    /**
     * @var bool
     */
    public $maskClosable = true;

    /**
     * @var string
     */
    public $okText = 'Sure';

    /**
     * @var string
     */
    public $cancelText = 'Cancel';

    /**
     * @var string
     */
    public $okType = Button::THEME_PRIMARY;

    /**
     * @var int
     */
    public $zIndex = 1000;

    /**
     * @var bool
     */
    public $closable = true;

    /**
     * @var array
     */
    public $maskStyle = [];

    /**
     * @var array
     */
    public $wrapStyle = [];

    /**
     * @var array
     */
    public $drawerStyle = [];

    /**
     * @var array
     */
    public $headerStyle = [];

    /**
     * @var array
     */
    public $bodyStyle = [];
}