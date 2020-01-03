<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Page;

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
    public $dress = [];

    /**
     * @var string
     */
    public $dressJson;

    /**
     * @var bool
     */
    public $border = true;

    /**
     * @var int
     */
    public $scroll = 2000;

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
     * @var int
     */
    public $dynamic = 0;
}