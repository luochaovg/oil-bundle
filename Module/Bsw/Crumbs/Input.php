<?php

namespace Leon\BswBundle\Module\Bsw\Crumbs;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\Crumbs\Entity\Crumb;
use Leon\BswBundle\Module\Bsw\Menu\Entity\Menu;

class Input extends ArgsInput
{
    /**
     * @var array
     */
    public $masterMenuDetail = [];

    /**
     * @var array
     */
    public $slaveMenuDetail = [];
}