<?php

namespace Leon\BswBundle\Module\Bsw\Filter;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var array
     */
    public $filter = [];

    /**
     * @var array
     */
    public $group = [];

    /**
     * @var array
     */
    public $diffuse = [];

    /**
     * @var array
     */
    public $condition = [];

    /**
     * @var Button[]
     */
    public $operates = [];

    /**
     * @var string
     */
    public $formatJson;

    /**
     * @var int
     */
    public $columnPx = 74;

    /**
     * @var int
     */
    public $maxShow = 5;

    /**
     * @var int
     */
    public $maxShowInIframe = 4;

    /**
     * @var array
     */
    public $showList = [];

    /**
     * @var string
     */
    public $showListJson;

    /**
     * @var array
     */
    public $showFull = [];

    /**
     * @var string
     */
    public $showFullJson;

    /**
     * @var string
     */
    public $textShow = 'Show filter';

    /**
     * @var string
     */
    public $textHide = 'Hide filter';
}