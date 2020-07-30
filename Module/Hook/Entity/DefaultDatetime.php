<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class DefaultDatetime extends Hook
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
        return $value;
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
        $value = strtotime($value);
        if ($value !== false) {
            return $value;
        }

        return date(current($args) ?: Abs::FMT_FULL);
    }
}