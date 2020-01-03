<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Switcher extends Form
{
    use Size;

    /**
     * @var string
     */
    protected $checkedChildren = 'Open';

    /**
     * @var string
     */
    protected $unCheckedChildren = 'Close';

    /**
     * @return string
     */
    public function getCheckedChildren(): string
    {
        return $this->checkedChildren;
    }

    /**
     * @param string $checkedChildren
     *
     * @return $this
     */
    public function setCheckedChildren(string $checkedChildren)
    {
        $this->checkedChildren = $checkedChildren;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnCheckedChildren(): string
    {
        return $this->unCheckedChildren;
    }

    /**
     * @param string $unCheckedChildren
     *
     * @return $this
     */
    public function setUnCheckedChildren(string $unCheckedChildren)
    {
        $this->unCheckedChildren = $unCheckedChildren;

        return $this;
    }
}