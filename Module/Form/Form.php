<?php

namespace Leon\BswBundle\Module\Form;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits\Attributes;
use Leon\BswBundle\Module\Form\Entity\Traits\ClassCss;
use Leon\BswBundle\Module\Form\Entity\Traits\Disabled;
use Leon\BswBundle\Module\Form\Entity\Traits\Field;
use Leon\BswBundle\Module\Form\Entity\Traits\Key;
use Leon\BswBundle\Module\Form\Entity\Traits\Placeholder;
use Leon\BswBundle\Module\Form\Entity\Traits\Rules;
use Leon\BswBundle\Module\Form\Entity\Traits\Style;
use Leon\BswBundle\Module\Form\Entity\Traits\Value;

abstract class Form
{
    use Value;
    use ClassCss;
    use Attributes;
    use Disabled;
    use Style;
    use Placeholder;
    use Rules;
    use Key;
    use Field;

    /**
     * @const string
     */
    const SIZE_SMALL  = 'small';
    const SIZE_MIDDLE = 'default';
    const SIZE_LARGE  = 'large';

    /**
     * @var string
     */
    const SCENE_COMMON = 'common';
    const SCENE_NORMAL = 'normal';
    const SCENE_IFRAME = 'iframe';

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return Helper::camelToUnder(Helper::clsName(static::class), '-');
    }
}