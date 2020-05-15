<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait SwitchFieldShape
{
    /**
     * @var array
     */
    protected $switchFieldShape = [];

    /**
     * @return array
     */
    public function getSwitchFieldShape(): array
    {
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