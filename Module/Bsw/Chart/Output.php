<?php

namespace Leon\BswBundle\Module\Bsw\Chart;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;

class Output extends ArgsOutput
{
    /**
     * @var Links[]
     */
    public $tabsMenu = [];

    /**
     * @var array
     */
    public $items = [];
}