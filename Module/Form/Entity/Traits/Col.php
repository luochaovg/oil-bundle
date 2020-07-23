<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait Col
{
    /**
     * @var array
     */
    protected $col = [
        //'xs'  => 24,
        //'sm'  => 24,
        //'md'  => 12,
        //'lg'  => 8,
        //'xl'  => 8,
        //'xxl' => 6,
    ];

    /**
     * @return int
     */
    public function getCol(): array
    {
        return $this->col;
    }

    /**
     * @return string
     */
    public function getColStringify(): string
    {
        $col = Helper::arrayMap(
            $this->col,
            function ($v, $k) {
                return ":{$k}=\"{$v}\"";
            }
        );

        return implode(' ', $col);
    }

    /**
     * @param array $col
     *
     * @return $this
     */
    public function setCol(array $col)
    {
        $this->col = $col;

        return $this;
    }

    /**
     * @param string $key
     * @param int    $number
     *
     * @return $this
     */
    public function setColNumber(string $key, int $number)
    {
        $this->col[$key] = $number;

        return $this;
    }
}