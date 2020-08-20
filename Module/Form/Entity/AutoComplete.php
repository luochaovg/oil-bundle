<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\BackFill;
use Leon\BswBundle\Module\Form\Entity\Traits\DataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\FilterOption;
use Leon\BswBundle\Module\Form\Entity\Traits\Search;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class AutoComplete extends Form
{
    use AllowClear;
    use BackFill;
    use DropdownStyle;
    use DataSource;
    use FilterOption;
    use Search;
    use Size;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setAllowClear(false);
        $this->setFilterOption('bsw.filterOptionForAutoComplete');
    }
}