<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class Money extends MoneyStringify
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
        $value /= static::REDOUBLE;

        return Helper::numberFormat($value, 2);
    }
}