<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\Enum;
use Leon\BswBundle\Module\Form\Entity\Traits\LabelInValue;
use Leon\BswBundle\Module\Form\Entity\Traits\Mode;
use Leon\BswBundle\Module\Form\Entity\Traits\NotFoundContent;
use Leon\BswBundle\Module\Form\Entity\Traits\OptionFilterProp;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowArrow;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\TokenSeparators;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForKey;
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
    use DropdownStyle;
    use VarNameForKey;

    /**
     * @var array
     */
    protected $switchFieldShape = [];

    /**
     * Select constructor.
     */
    public function __construct()
    {
        $this->setButtonLabel('Popup for select');
        $this->setMode(Abs::MODE_DEFAULT);
        $this->setOptionFilterProp(Abs::SEARCH_LABEL);

        $this->setVarNameForKey('persistenceSwitchField');
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
     * @return array
     */
    public function getSwitchFieldShape(): array
    {
        if (empty($this->switchFieldShape)) {
            return $this->switchFieldShape;
        }

        $this->setAllowClear(false);
        $this->setChange('switchFieldShapeWithSelect');

        foreach ($this->switchFieldShape as &$item) {
            $item = array_map('strval', (array)$item);
        }

        return $this->switchFieldShape;
    }

    /**
     * @param array $switchFieldShape
     *
     * @return $this
     */
    public function setSwitchFieldShape(array $switchFieldShape)
    {
        $this->switchFieldShape = $switchFieldShape;

        return $this;
    }

    /**
     * @param array $switchFieldShape
     *
     * @return $this
     */
    public function appendSwitchFieldShape(array $switchFieldShape)
    {
        $this->switchFieldShape = array_merge($this->switchFieldShape, $switchFieldShape);

        return $this;
    }
}