<?php

namespace Leon\BswBundle\Module\Bsw\Operate;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Form\Form;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $operatesSize = Form::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $position = null;
}