<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class RateStringify extends MoneyStringify
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
        $tpl = $extraArgs['tpl'] ?? '%.2f %%';

        return sprintf($tpl, Helper::numberFormat($value, 2));
    }
}