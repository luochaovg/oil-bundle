<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class Timestamp extends Hook
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
        $scene = $extraArgs['scene'] ?? null;
        if ($scene === 'filter' && empty($value)) {
            return null;
        }

        if (empty($value) && !empty($extraArgs["{$scene}_empty"])) {
            $value = $extraArgs["{$scene}_empty"];
        }

        return date(current($args) ?: Abs::FMT_FULL, $value);
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
        if (!empty($value)) {
            return strtotime($value);
        }

        return $value ?: time();
    }
}