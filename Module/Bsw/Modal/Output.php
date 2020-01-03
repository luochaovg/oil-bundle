<?php

namespace Leon\BswBundle\Module\Bsw\Modal;

use Leon\BswBundle\Module\Bsw\ArgsOutput;

class Output extends ArgsOutput
{
    /**
     * @var string
     */
    public $title = 'Modal';

    /**
     * @var string
     */
    public $okText = 'Sure';

    /**
     * @var string
     */
    public $width = '50%';

    /**
     * @var bool
     */
    public $footer = false;

    /**
     * @var string
     */
    public $wrapClassName = null;
}