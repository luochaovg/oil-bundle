<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Form;

class Group extends Form
{
    /**
     * @var Form[]
     */
    protected $member = [];

    /**
     * @var array
     */
    protected $column = [];

    /**
     * @var int|array
     */
    protected $gutter = 8;

    /**
     * @return Form[]
     */
    public function getMember(): array
    {
        return $this->member;
    }

    /**
     * @param Form[] $member
     *
     * @return $this
     */
    public function setMember(array $member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @param Form $member
     *
     * @return $this
     */
    public function pushMember(Form $member)
    {
        array_push($this->member, $member);

        return $this;
    }

    /**
     * @return array
     */
    public function getColumn(): array
    {
        $count = count($this->member);
        $default = array_fill(0, $count, floor(24 / $count));

        if (empty($this->column)) {
            return $default;
        }

        if (array_sum($this->column) !== 24) {
            return $default;
        }

        if (count($this->column) != count($this->member)) {
            return $default;
        }

        return $this->column;
    }

    /**
     * @param array $column
     *
     * @return $this
     */
    public function setColumn(array $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getGutter()
    {
        if (is_int($this->gutter)) {
            return $this->gutter;
        }

        return Helper::jsonStringify($this->gutter);
    }

    /**
     * @param array|int $gutter
     *
     * @return $this
     */
    public function setGutter($gutter)
    {
        $this->gutter = $gutter;

        return $this;
    }
}