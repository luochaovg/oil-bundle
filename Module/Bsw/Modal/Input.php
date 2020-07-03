<?php

namespace Leon\BswBundle\Module\Bsw\Modal;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $title = 'Modal';

    /**
     * @var string
     */
    public $width = '50%';

    /**
     * @var bool
     */
    public $footer = false;

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
    public $maskClosable = false;

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
    public $bodyStyle = [];

    /**
     * @var array
     */
    public $maskStyle = [];

    /**
     * @var array
     */
    public $dialogStyle = [];

    /**
     * @var string
     */
    public $okCallback;

    /**
     * @var string
     */
    public $cancelCallback;

    /**
     * @var string
     */
    public $afterCloseCallback;
}