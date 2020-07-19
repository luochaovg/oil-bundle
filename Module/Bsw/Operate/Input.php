<?php

namespace Leon\BswBundle\Module\Bsw\Operate;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $operatesSize = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $operatesSizeInMobile = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $position = Abs::POS_TOP;

    /**
     * @var string
     */
    public $className = 'bsw-align-right';

    /**
     * @var string
     */
    public $classNameInIFrame = 'bsw-align-left';
}