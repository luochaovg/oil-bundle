<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;

class Input extends ArgsInput
{
    /**
     * @var array
     */
    public $preview = [];

    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var int
     */
    public $dynamic = 0;
}