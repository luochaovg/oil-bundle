<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var bool
     */
    public $choiceFixed;

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
    public $customRenders = [];

    /**
     * @var string
     */
    public $customRendersJson;

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
     * @var bool
     */
    public $border;

    /**
     * @var string
     */
    public $childrenName;

    /**
     * @var bool
     */
    public $expandRows;

    /**
     * @var bool
     */
    public $expandRowByClick;

    /**
     * @var int
     */
    public $expandIconColumnIndex;

    /**
     * @var int
     */
    public $indentSize;

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
     * @var string
     */
    public $paginationClsName;

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
    public $rowClsNameMethod;

    /**
     * @var bool
     */
    public $header;

    /**
     * @var bool
     */
    public $footer;

    /**
     * @var bool
     */
    public $scrollXOperate;
}