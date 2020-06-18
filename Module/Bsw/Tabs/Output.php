<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var Links[]
     */
    public $links = [];

    /**
     * @var bool
     */
    public $fit = true;

    /**
     * @var string
     */
    public $size = Button::SIZE_MIDDLE;
}