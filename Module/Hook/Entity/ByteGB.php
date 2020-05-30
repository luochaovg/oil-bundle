<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class ByteGB extends ByteMB
{
    /**
     * @const int
     */
    const REDOUBLE = Abs::HEX_SIZE ** 3;
}