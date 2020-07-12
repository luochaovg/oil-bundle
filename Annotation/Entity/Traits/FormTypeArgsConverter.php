<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Input;
use Symfony\Component\Validator\Constraints\Length;

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
        if (!is_array($value)) {
            $this->exception('typeArgs', 'must be array type');
        }

        $isInputItem = ($this->item->type instanceof Input);
        $length = $this->items[Length::class]->max ?? null;
        if (($this->scene != Abs::TAG_FILTER) && $isInputItem && !isset($value['maxLength']) && $length) {
            $value['maxLength'] = $length;
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