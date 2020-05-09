<?php

namespace Leon\BswBundle\Module\Form\Entity;

class Date extends Datetime
{
    /**
     * Date constructor.
     */
    public function __construct()
    {
        $this->setFormat('YYYY-MM-DD');
        $this->setShowTime(false);
    }
}