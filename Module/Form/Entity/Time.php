<?php

namespace Leon\BswBundle\Module\Form\Entity;

class Time extends Datetime
{
    /**
     * Time constructor.
     */
    public function __construct()
    {
        $this->setFormat('HH:mm:ss');
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'time-picker';
    }
}