<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var bool
     */
    public $border = true;

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
    public $clsName = 'previewRowClsName';

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
}