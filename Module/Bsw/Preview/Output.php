<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;

class Output extends ArgsOutput
{
    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var string
     */
    public $columnsJson;

    /**
     * @var array
     */
    public $list = [];

    /**
     * @var string
     */
    public $listJson;

    /**
     * @var array
     */
    public $slots = [];

    /**
     * @var string
     */
    public $slotsJson;

    /**
     * @var bool
     */
    public $border;

    /**
     * @var int
     */
    public $scrollX = 2000;

    /**
     * @var array
     */
    public $scroll = [];

    /**
     * @var bool
     */
    public $size;

    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var array
     */
    public $page = [];

    /**
     * @var string
     */
    public $pageJson;

    /**
     * @var array
     */
    public $pageSizeOptions;

    /**
     * @var string
     */
    public $pageSizeOptionsJson;

    /**
     * @var int
     */
    public $dynamic;

    /**
     * @var array
     */
    public $query = [];

    /**
     * @var string
     */
    public $clsName;

    /**
     * @var bool
     */
    public $header;

    /**
     * @var bool
     */
    public $footer;
}