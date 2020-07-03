<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;

class Output extends ArgsOutput
{
    /**
     * @var Links[]
     */
    public $links = [];

    /**
     * @var bool
     */
    public $fit;

    /**
     * @var string
     */
    public $size;
}