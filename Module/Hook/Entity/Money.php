<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;

class Money extends Hook
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
        $value /= static::REDOUBLE;

        return Helper::numberFormat($value, 2);
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
        return intval(bcmul(Helper::numericValue($value), static::REDOUBLE));
    }
}