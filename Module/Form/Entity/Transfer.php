<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\DataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\FilterOption;
use Leon\BswBundle\Module\Form\Entity\Traits\ListStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\SelectChange;
use Leon\BswBundle\Module\Form\Entity\Traits\SelectedKeys;
use Leon\BswBundle\Module\Form\Entity\Traits\SelectedKeysKey;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSelectAll;
use Leon\BswBundle\Module\Form\Entity\Traits\SourceOperate;
use Leon\BswBundle\Module\Form\Entity\Traits\SourceTitle;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetKeys;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetKeysKey;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetOperate;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetTitle;
use Leon\BswBundle\Module\Form\Form;

class Transfer extends Form
{
    use DataSource;
    use SourceTitle;
    use TargetTitle;
    use SourceOperate;
    use TargetOperate;
    use SelectedKeys;
    use SelectedKeysKey;
    use SelectChange;
    use TargetKeys;
    use TargetKeysKey;
    use ShowSearch;
    use FilterOption;
    use ShowSelectAll;
    use ListStyle;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setFilterOption('bsw.filterOptionForTransfer');
    }
}