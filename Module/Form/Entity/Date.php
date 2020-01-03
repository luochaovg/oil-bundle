<?php

namespace Leon\BswBundle\Module\Form\Entity;

class Date extends Datetime
{
    /**
     * @var string
     */
    protected $format = 'YYYY-MM-DD';

    /**
     * @var bool
     */
    protected $showTime = false;
}