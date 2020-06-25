<?php

namespace Leon\BswBundle\Module\Bsw\Filter;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Form\Form;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $key = 'filter';

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
     * @var string
     */
    public $textShow = 'Show filter';

    /**
     * @var string
     */
    public $textHide = 'Hide filter';

    /**
     * @var string
     */
    public $filterFormSize = Form::SIZE_MIDDLE;
}