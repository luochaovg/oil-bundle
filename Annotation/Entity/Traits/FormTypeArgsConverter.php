<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait FormTypeArgsConverter
{
    /**
     * @param $value
     *
     * @return array
     * @throws
     */
    protected function typeArgs($value)
    {
        if (empty($value)) {
            return [];
        }

        if (!is_array($value)) {
            $this->exception('typeArgs', 'must be array type');
        }

        $form = $this->item->type;
        foreach ($value as $key => $val) {
            $fn = 'set' . Helper::underToCamel($key, false);
            if (!method_exists($form, $fn)) {
                $this->exception(
                    'typeArgs',
                    "item named `{$key}` don't exists class attribute in " . get_class($form)
                );
            }
            $form->{$fn}($val);
        }

        return $value;
    }
}