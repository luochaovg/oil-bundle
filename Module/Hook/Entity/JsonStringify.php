<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class JsonStringify extends Json
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        return Helper::formatPrintJson(parent::preview($value, $args, $extraArgs), 4, ': ');
    }
}