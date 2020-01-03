<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait RulesConverter
{
    /**
     * @param $value
     *
     * @return array
     */
    protected function rules($value)
    {
        if (is_string($value)) {
            $value = Helper::stringToArray($value, true, true, null, '|');
            $rulesArr = [];
            foreach ($value as $rule) {
                $rule = Helper::stringToArray($rule, true, false);
                $rulesArr[array_shift($rule)] = $rule;
            }
            $value = $rulesArr;
        }

        if (!is_array($value)) {
            $this->exception('rules', 'should be string or array');
        }

        $_value = [];
        foreach ($value as $fn => $args) {
            is_int($fn) && list($fn, $args) = [$args, []];
            $_value[$fn] = (array)$args;
        }

        return $_value;
    }
}