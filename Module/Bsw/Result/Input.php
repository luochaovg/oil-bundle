<?php

namespace Leon\BswBundle\Module\Bsw\Result;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

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
    public $status = Abs::RESULT_STATUS_SUCCESS;

    /**
     * @var string
     */
    public $okText = 'I got it';

    /**
     * @var bool
     */
    public $okShow = true;

    /**
     * @var string
     */
    public $cancelText = 'Cancel';

    /**
     * @var bool
     */
    public $cancelShow = false;

    /**
     * @var string
     */
    public $okType = Abs::THEME_PRIMARY;
}