<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class HourDuration extends Hook
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
        if (!Helper::isIntNumeric($value)) {
            return null;
        }

        // return Helper::humanDuration($value);

        $date = date(Abs::FMT_FULL, time() + $value * 3600);
        [$_, $info] = Helper::gapDateDetail($date, $extraArgs['digit'] ?? []);

        return $info;
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
        return $value;
    }
}