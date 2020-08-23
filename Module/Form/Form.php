<?php

namespace Leon\BswBundle\Module\Form;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits\Attributes;
use Leon\BswBundle\Module\Form\Entity\Traits\AutoFocus;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\Change;
use Leon\BswBundle\Module\Form\Entity\Traits\ClassCss;
use Leon\BswBundle\Module\Form\Entity\Traits\Disabled;
use Leon\BswBundle\Module\Form\Entity\Traits\DynamicDataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\Field;
use Leon\BswBundle\Module\Form\Entity\Traits\FormData;
use Leon\BswBundle\Module\Form\Entity\Traits\Key;
use Leon\BswBundle\Module\Form\Entity\Traits\Label;
use Leon\BswBundle\Module\Form\Entity\Traits\Name;
use Leon\BswBundle\Module\Form\Entity\Traits\Placeholder;
use Leon\BswBundle\Module\Form\Entity\Traits\FormRules;
use Leon\BswBundle\Module\Form\Entity\Traits\Style;
use Leon\BswBundle\Module\Form\Entity\Traits\Value;

abstract class Form
{
    use Key;
    use Field;
    use Name;
    use Label;
    use Value;
    use ClassCss;
    use Attributes;
    use Disabled;
    use Style;
    use Placeholder;
    use FormRules;
    use Change;
    use AutoFocus;
    use FormData;
    use DynamicDataSource;
    use ButtonStyle;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return Helper::camelToUnder(Helper::clsName(static::class), '-');
    }
}