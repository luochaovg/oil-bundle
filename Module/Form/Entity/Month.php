<?php

namespace Leon\BswBundle\Module\Form\Entity;

class Month extends Datetime
{
    /**
     * Date constructor.
     */
    public function __construct()
    {
        $this->setFormat('YYYY-MM');
        $this->setShowTime(false);
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'month-picker';
    }
}