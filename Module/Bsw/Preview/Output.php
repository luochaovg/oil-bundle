<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Entity\Abs;

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
    public $border = true;

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
    public $size = 'default'; // default、small

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
    public $pageSizeOptions = Abs::PG_PAGE_SIZE_OPTIONS;

    /**
     * @var string
     */
    public $pageSizeOptionsJson;

    /**
     * @var int
     */
    public $dynamic = 0;

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
    public $header = false;

    /**
     * @var bool
     */
    public $footer = false;
}