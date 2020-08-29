<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;

class Month extends Datetime
{
    /**
     * Date constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
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