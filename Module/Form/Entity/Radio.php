<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Col;
use Leon\BswBundle\Module\Form\Entity\Traits\Enum;
use Leon\BswBundle\Module\Form\Form;

class Radio extends Form
{
    use Enum;
    use Col;
}