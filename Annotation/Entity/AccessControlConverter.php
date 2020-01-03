<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\AnnotationConverter;

/**
 * @property AccessControl $item
 */
class AccessControlConverter extends AnnotationConverter
{
    /**
     * @param $value
     *
     * @return array
     */
    protected function freeRole($value)
    {
        return array_map('intval', (array)$value);
    }
}