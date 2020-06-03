<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;

/**
 * @Annotation
 */
class Preview extends Annotation
{
    /**
     * @var number
     */
    public $sort = 99;

    /**
     * @var bool
     */
    public $show = true;

    /**
     * @var string|array
     */
    public $hook;

    /**
     * @var string
     */
    public $label;

    /**
     * @var bool Need trans for label?
     */
    public $trans;

    /**
     * @var string
     */
    public $align;

    /**
     * @var false|string
     */
    public $fixed;

    /**
     * @var int
     */
    public $width;

    /**
     * @var array|string (Priority over render)
     * @license Enum{"pink", "red", "orange", "green", "cyan", "blue", "purple", "#color"}
     */
    public $dress;

    /**
     * @var string For vue-slot (Lowest priority)
     */
    public $render;

    /**
     * @var bool
     */
    public $status = false;

    /**
     * @var array|bool
     */
    public $enum;

    /**
     * @var array|bool|string
     */
    public $enumExtra;

    /**
     * @var string|array|callable
     */
    public $enumHandler;

    /**
     * @var bool
     */
    public $html = false;
}