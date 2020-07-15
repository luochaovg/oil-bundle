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
        if ($scene === Abs::TAG_FILTER && empty($value)) {
            return null;
        }

        if (empty($value)) {
            $zero = trim("{$scene}_zero", '_');
            if (!empty($extraArgs[$zero])) { // default value
                return $extraArgs[$zero];
            }

            $empty = trim("{$scene}_empty", '_');
            if (!empty($extraArgs[$empty])) { // default timestamp
                $value = $extraArgs[$empty];
            }
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