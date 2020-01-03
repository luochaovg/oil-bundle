<?php

namespace Leon\BswBundle\Module\Bsw\Header;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Bsw\Header\Entity\Setting;

class Output extends ArgsOutput
{
    /**
     * @var Setting[]
     */
    public $setting = [];

    /**
     * @var Links[]
     */
    public $links = [];
}