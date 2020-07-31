<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;

class MoneyStringify extends Money
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
        $tpl = $extraArgs['tpl'] ?? '%s';

        return Helper::money($value, $tpl);
    }
}