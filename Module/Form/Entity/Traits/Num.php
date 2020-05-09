<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Num
{
    /**
     * @var int
     */
    protected $num = 0;

    /**
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * @param int $num
     *
     * @return $this
     */
    public function setNum(int $num)
    {
        if ($num >= 0 && $num <= 24) {
            $this->num = $num;
        }

        return $this;
    }
}