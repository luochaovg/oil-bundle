<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var array
     */
    public $record = [];

    /**
     * @var Button[]
     */
    public $operates = [];

    /**
     * @var string
     */
    public $formatJson;

    /**
     * @var array
     */
    public $dataKeys = [];
}