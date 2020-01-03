<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;

class MoneyStringify extends Hook
{
    /**
     * @const int
     */
    const REDOUBLE = 100;

    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function preview($value, array $args, array $extraArgs = [])
    {
        $value /= self::REDOUBLE;
        $tpl = $extraArgs['tpl'] ?? '%s';

        return Helper::money($value, $tpl);
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
        return intval(bcmul(Helper::numericValue($value), self::REDOUBLE));
    }
}