<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Input extends ArgsInput
{
    /**
     * @var bool
     */
    public $fit = true;

    /**
     * @var string
     */
    public $size = Button::SIZE_MIDDLE;
}