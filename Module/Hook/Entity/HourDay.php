<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Hook\Hook;

class HourDay extends Hook
{
    /**
     * @const int
     */
    const REDOUBLE = 24;

    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function preview($value, array $args, array $extraArgs = [])
    {
        return $value / static::REDOUBLE;
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
        return $value * static::REDOUBLE;
    }
}