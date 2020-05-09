<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\Format;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowTime;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Datetime extends Form
{
    use Size;
    use AllowClear;
    use Format;
    use ShowTime;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'date-picker';
    }
}