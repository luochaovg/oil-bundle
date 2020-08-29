<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;

class Date extends Datetime
{
    /**
     * Date constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setFormat('YYYY-MM-DD');
        $this->setShowTime(false);
    }
}