<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\AnnotationConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumExtraConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumHandlerConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FormTypeArgsConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FormTypeConverter;
use Leon\BswBundle\Annotation\Entity\Traits\HookConverter;
use Leon\BswBundle\Annotation\Entity\Traits\TransConverter;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Filter\Entity\Accurate;
use Leon\BswBundle\Module\Filter\Entity\Like;
use Symfony\Component\Validator\Constraints\Type;
use Leon\BswBundle\Module\Filter\Filter as BswFilter;

/**
 * @property Mixed $item
 */
class MixedConverter extends AnnotationConverter
{
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function field($value)
    {
        return $value ?: $this->target;
    }
}