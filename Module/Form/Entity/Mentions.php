<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\Enum;
use Leon\BswBundle\Module\Form\Entity\Traits\FilterOption;
use Leon\BswBundle\Module\Form\Entity\Traits\Placement;
use Leon\BswBundle\Module\Form\Entity\Traits\Prefix;
use Leon\BswBundle\Module\Form\Entity\Traits\Rows;
use Leon\BswBundle\Module\Form\Entity\Traits\Separator;
use Leon\BswBundle\Module\Form\Form;

class Mentions extends Form
{
    use Enum;
    use FilterOption;
    use Placement;
    use Prefix;
    use Separator;
    use Rows;

    /**
     * Mentions constructor.
     */
    public function __construct()
    {
        $this->setPlacement(Abs::POS_BOTTOM);
        $this->setPrefix('@');
        $this->setSeparator(' ');
        $this->setFilterOption('');
        $this->setFilterOption('bsw.filterOptionForMentions');
    }
}