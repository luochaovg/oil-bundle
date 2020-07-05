<?php

namespace Leon\BswBundle\Module\Bsw\Result;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $title = 'Operation success';

    /**
     * @var string
     */
    public $subTitle;

    /**
     * @var bool
     */
    public $centered = true;

    /**
     * @var string|int
     */
    public $width = Abs::MEDIA_MIN;

    /**
     * @var string
     */
    public $status = Abs::RESULT_SUCCESS;

    /**
     * @var string
     */
    public $operatorType = Button::THEME_PRIMARY;

    /**
     * @var string
     */
    public $operatorClick = 'result.visible = false';

    /**
     * @var string
     */
    public $operator = 'I got it';
}