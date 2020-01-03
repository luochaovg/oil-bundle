<?php

namespace Leon\BswBundle\Module\Form\Entity;

class Time extends Datetime
{
    /**
     * @var string
     */
    protected $format = 'HH:mm:ss';

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'time-picker';
    }
}