<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\Enum;
use Leon\BswBundle\Module\Form\Entity\Traits\LabelInValue;
use Leon\BswBundle\Module\Form\Entity\Traits\Mode;
use Leon\BswBundle\Module\Form\Entity\Traits\NotFoundContent;
use Leon\BswBundle\Module\Form\Entity\Traits\OptionFilterProp;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowArrow;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\SwitchFieldShape;
use Leon\BswBundle\Module\Form\Entity\Traits\TokenSeparators;
use Leon\BswBundle\Module\Form\Form;

class Select extends Form
{
    use Size;
    use Enum;
    use PreviewRoute;
    use AllowClear;
    use ButtonLabel;
    use NotFoundContent;
    use LabelInValue;
    use Mode;
    use ShowSearch;
    use ShowArrow;
    use OptionFilterProp;
    use TokenSeparators;
    use SwitchFieldShape;

    /**
     * @const string
     */
    const MODE_DEFAULT  = 'default';
    const MODE_MULTIPLE = 'multiple';
    const MODE_TAGS     = 'tags';
    const MODE_BOX      = 'combobox';

    const SEARCH_VALUE = 'value';
    const SEARCH_LABEL = 'children';

    /**
     * Select constructor.
     */
    public function __construct()
    {
        $this->setButtonLabel('Popup for select');
        $this->setMode(self::MODE_DEFAULT);
        $this->setOptionFilterProp(self::SEARCH_LABEL);
    }

    /**
     * @return bool
     */
    public function isValueMultiple(): bool
    {
        return is_array($this->value) || is_object($this->value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (is_array($this->value)) {
            return Helper::jsonStringify(array_map('strval', $this->value));
        }

        return $this->value;
    }

    /**
     * @return bool
     */
    public function isAllowClear(): bool
    {
        if ($this->getSwitchFieldShape()) {
            return false;
        }

        return $this->allowClear;
    }
}