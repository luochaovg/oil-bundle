<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;

class Input extends ArgsInput
{
    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var bool
     */
    public $choiceFixed = true;

    /**
     * @var bool
     */
    public $border = true;

    /**
     * @var bool
     */
    public $borderInMobile = true;

    /**
     * @var string
     */
    public $childrenName = Abs::TAG_CHILDREN;

    /**
     * @var bool
     */
    public $expandRows = false;

    /**
     * @var bool
     */
    public $expandRowByClick = false;

    /**
     * @var int
     */
    public $expandIconColumnIndex;

    /**
     * @var int
     */
    public $indentSize = 20;

    /**
     * @var array
     */
    public $scroll = [];

    /**
     * @var bool
     */
    public $removeOperateInIframe = true;

    /**
     * @var bool
     */
    public $size = Abs::SIZE_DEFAULT;

    /**
     * @var bool
     */
    public $sizeInMobile = Abs::SIZE_DEFAULT;

    /**
     * @var array
     */
    public $pageSizeOptions = Abs::PG_PAGE_SIZE_OPTIONS;

    /**
     * @var int
     */
    public $dynamic = 0;

    /**
     * @var string
     */
    public $rowClsNameMethod = 'previewRowClsName';

    /**
     * @var string
     */
    public $recordOperatesSize = Abs::SIZE_SMALL;

    /**
     * @var bool
     */
    public $header = false;

    /**
     * @var bool
     */
    public $footer = false;

    /**
     * @var bool|string
     */
    public $childrenRelationField = false;

    /**
     * @var string
     */
    public $childrenRowClsName = 'bsw-row-children';
}