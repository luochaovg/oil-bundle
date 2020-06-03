<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\ExpandAll;
use Leon\BswBundle\Module\Form\Entity\Traits\LabelInValue;
use Leon\BswBundle\Module\Form\Entity\Traits\OptionFilterProp;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowArrow;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowCheckedStrategy;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\TreeData;
use Leon\BswBundle\Module\Form\Form;

class SelectTree extends Form
{
    use Size;
    use AllowClear;
    use LabelInValue;
    use ShowSearch;
    use ShowArrow;
    use ShowCheckedStrategy;
    use OptionFilterProp;
    use DropdownStyle;
    use TreeData;
    use ExpandAll;

    const SEARCH_VALUE = 'value';
    const SEARCH_TITLE = 'title';

    const CHECKED_STRATEGY_ALL      = 'bsw.d.TreeSelect.SHOW_ALL';
    const CHECKED_STRATEGY_PARENT   = 'bsw.d.TreeSelect.SHOW_PARENT';
    const CHECKED_STRATEGY_CHILDREN = 'bsw.d.TreeSelect.SHOW_CHILD';

    /**
     * Select constructor.
     */
    public function __construct()
    {
        $this->setOptionFilterProp(self::SEARCH_TITLE);
        $this->setShowCheckedStrategy(self::CHECKED_STRATEGY_ALL);
    }
}