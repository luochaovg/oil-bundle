<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var int
     */
    public $id = 0;

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
    public $fileListKeyCollect = [];

    /**
     * @var string
     */
    public $fileListKeyCollectJson;

    /**
     * @var array
     */
    public $uploadTipsCollect = [];

    /**
     * @var string
     */
    public $uploadTipsCollectJson;
}