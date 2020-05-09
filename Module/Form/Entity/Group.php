<?php

namespace Leon\BswBundle\Module\Form\Entity;

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
    protected $memberCol = [];

    /**
     * @return Form[]
     */
    public function getMember(): array
    {
        $memberCol = $this->getMemberCol();
        if (empty($memberCol)) {
            $percent = floor(100 / count($this->member));
            foreach ($this->member as $index => $item) {
                array_push($memberCol, $percent - 5);
                array_push($memberCol, '8px');
            }
        }

        foreach ($this->member as $index => $item) {
            if (!$item->getKey()) {
                $item->setField("_{$index}");
            }
            if (!$item->getPlaceholder()) {
                $item->setPlaceholder($this->getPlaceholder());
            }

            $itemIndex = $index * 2;
            $marginIndex = $itemIndex + 1;

            if (!$item->hasStyle('width') && $col = ($memberCol[$itemIndex] ?? null)) {
                $item->appendStyle(['width' => is_numeric($col) ? "{$col}%" : $col]);
            }
            if (!$item->hasStyle('margin-right') && $col = ($memberCol[$marginIndex] ?? null)) {
                $item->appendStyle(['margin-right' => is_numeric($col) ? "{$col}%" : $col]);
            }
        }

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
     * @param Form[] $member
     *
     * @return $this
     */
    public function appendMember(array $member)
    {
        $this->member = array_merge($this->member, $member);

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
    public function getMemberCol(): array
    {
        return $this->memberCol;
    }

    /**
     * @param array $memberCol
     *
     * @return $this
     */
    public function setMemberCol(array $memberCol)
    {
        $this->memberCol = $memberCol;

        return $this;
    }

    /**
     * @param array $memberCol
     *
     * @return $this
     */
    public function appendMemberCol(array $memberCol)
    {
        $this->memberCol = array_merge($this->memberCol, $memberCol);

        return $this;
    }
}