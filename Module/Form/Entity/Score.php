<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Form;

class Score extends Form
{
    /**
     * @var bool
     */
    protected $allowClear = false;

    /**
     * @var bool
     */
    protected $allowHalf = false;

    /**
     * @var string
     */
    protected $character;

    /**
     * @var int
     */
    protected $count = 10;

    /**
     * @return bool
     */
    public function isAllowClear(): bool
    {
        return $this->allowClear;
    }

    /**
     * @param bool $allowClear
     *
     * @return $this
     */
    public function setAllowClear(bool $allowClear = true)
    {
        $this->allowClear = $allowClear;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowHalf(): bool
    {
        return $this->allowHalf;
    }

    /**
     * @param bool $allowHalf
     *
     * @return $this
     */
    public function setAllowHalf(bool $allowHalf = true)
    {
        $this->allowHalf = $allowHalf;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharacter(): ?string
    {
        return $this->character;
    }

    /**
     * @param string $character
     *
     * @return $this
     */
    public function setCharacter(string $character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setCount(int $count)
    {
        $this->count = $count;

        return $this;
    }
}