<?php

namespace Leon\BswBundle\Module\Bsw\Drawer;

use Leon\BswBundle\Module\Bsw\ArgsOutput;

class Output extends ArgsOutput
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string|int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $placement;

    /**
     * @var string
     */
    public $wrapClassName;

    /**
     * @var bool
     */
    public $keyboard;

    /**
     * @var bool
     */
    public $mask;

    /**
     * @var bool
     */
    public $maskClosable;

    /**
     * @var string
     */
    public $okText;

    /**
     * @var string
     */
    public $cancelText;

    /**
     * @var string
     */
    public $okType;

    /**
     * @var int
     */
    public $zIndex;

    /**
     * @var bool
     */
    public $closable;

    /**
     * @var string
     */
    public $maskStyleJson;

    /**
     * @var string
     */
    public $wrapStyleJson;

    /**
     * @var string
     */
    public $drawerStyleJson;

    /**
     * @var string
     */
    public $headerStyleJson;

    /**
     * @var string
     */
    public $bodyStyleJson;
}