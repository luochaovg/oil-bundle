<?php

namespace Leon\BswBundle\Module\Bsw\Filter;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $key = Abs::TAG_FILTER;

    /**
     * @var int
     */
    public $columnPx = 74;

    /**
     * @var int
     */
    public $maxShow = 5;

    /**
     * @var int
     */
    public $maxShowInIframe = 4;

    /**
     * @var int
     */
    public $maxShowInMobile = 2;

    /**
     * @var string
     */
    public $textShow = 'Show filter';

    /**
     * @var string
     */
    public $textHide = 'Hide filter';

    /**
     * @var bool
     */
    public $showExport = true;

    /**
     * @var bool
     */
    public $showLabel = true;

    /**
     * @var string
     */
    public $filterFormSize = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $filterFormSizeInMobile = Abs::SIZE_DEFAULT;
}