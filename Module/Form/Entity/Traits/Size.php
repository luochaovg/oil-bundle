<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait Size
{
    /**
     * @var string
     */
    protected $size = Abs::SIZE_LARGE;

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     *
     * @return $this
     */
    public function setSize(string $size)
    {
        $this->size = $size;

        return $this;
    }
}