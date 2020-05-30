<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Module\Bsw\ArgsInput;

class Input extends ArgsInput
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var callable
     */
    public $handler;

    /**
     * @var array
     */
    public $submit = [];

    /**
     * @var string
     */
    public $fill = 'fill';

    /**
     * @var array
     */
    public $style = [];

    /**
     * @var string
     */
    public $i18nNewly = 'Newly record done';

    /**
     * @var string
     */
    public $i18nModify = 'Modify record done';

    /**
     * @var string
     */
    public $nextRoute = '';
}