<?php

namespace Leon\BswBundle\Module\Bsw\Chart;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;

class Input extends ArgsInput
{
    /**
     * @var callable
     */
    public $handler;

    /**
     * @var Links[]
     */
    public $tabsMenu = [];

    /**
     * @var array
     */
    public $items = [];

    /**
     * @var array
     */
    public $condition = [];
}