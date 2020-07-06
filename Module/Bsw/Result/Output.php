<?php

namespace Leon\BswBundle\Module\Bsw\Result;

use Leon\BswBundle\Module\Bsw\ArgsOutput;

class Output extends ArgsOutput
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $subTitle;

    /**
     * @var bool
     */
    public $centered;

    /**
     * @var string|int
     */
    public $width;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $okText;

    /**
     * @var bool
     */
    public $okShow;

    /**
     * @var string
     */
    public $cancelText;

    /**
     * @var bool
     */
    public $cancelShow;

    /**
     * @var string
     */
    public $okType;
}