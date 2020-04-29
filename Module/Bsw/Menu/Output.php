<?php

namespace Leon\BswBundle\Module\Bsw\Menu;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Menu\Entity\Menu;

class Output extends ArgsOutput
{
    /**
     * @var Menu[]
     */
    public $masterMenu = [];

    /**
     * @var Menu[][]
     */
    public $slaveMenu = [];

    /**
     * @var array
     */
    public $masterMenuDetail = [];

    /**
     * @var array
     */
    public $slaveMenuDetail = [];

    /**
     * @var int
     */
    public $parent = 0;

    /**
     * @var int
     */
    public $current = 0;
}