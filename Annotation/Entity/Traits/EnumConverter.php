<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait EnumConverter
{
    /**
     * @param $value
     *
     * @return mixed|void
     */
    protected function enum($value)
    {
        if (is_array($value)) {
            return $value;
        }

        $EnumClass = $this->extraArgs['enumClass'] ?? 'UnknownEnumClass';

        if (is_string($value)) {
            if (!defined($express = "{$EnumClass}::{$value}")) {
                $this->exception(
                    'enum',
                    "should be array, true or string (constant name in AcmeController::\$enum)"
                );
            }

            return constant($express);
        }

        if ($value !== true) {
            return null;
        }

        $flag = Helper::clsName($this->class) . "_{$this->target}";
        $prefer = strtoupper(Helper::camelToUnder($flag));
        $secondary = strtoupper(Helper::camelToUnder($this->target));

        if (defined($express = "{$EnumClass}::{$prefer}")) {
            return constant($express);
        }

        if (defined($express = "{$EnumClass}::{$secondary}")) {
            return constant($express);
        }

        $msg = "otherwise you should defined constant\n\n{$EnumClass}::{$prefer}\n{$EnumClass}::{$secondary}";
        $this->exception('enum', "should be array.\n{$msg}");
    }
}