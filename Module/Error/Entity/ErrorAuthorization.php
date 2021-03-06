<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAuthorization extends Error
{
    /**
     * @const int
     */
    const CODE = 4902;

    /**
     * @var string
     */
    protected $tiny = 'Authorization failed';
}