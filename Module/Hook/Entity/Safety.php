<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Hook\Hook;

class Safety extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function preview($value, array $args, array $extraArgs = [])
    {
        return Html::cleanHtml($value);
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function persistence($value, array $args, array $extraArgs = [])
    {
        return Html::cleanHtml($value);
    }
}