<?php

namespace Leon\BswBundle\Module\Bsw\Crumbs;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\Crumbs\Entity\Crumb;

class Input extends ArgsInput
{
    /**
     * @var Crumb[]
     */
    public $crumbs = [];
}