<?php

namespace Leon\BswBundle\Module\Form\Entity;

class TextArea extends Input
{
    /**
     * @var int
     */
    protected $minRows = 4;

    /**
     * @var int
     */
    protected $maxRows = 10;

    /**
     * @return int
     */
    public function getMinRows(): int
    {
        return $this->minRows;
    }

    /**
     * @param int $minRows
     *
     * @return $this
     */
    public function setMinRows(int $minRows)
    {
        $this->minRows = $minRows;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRows(): int
    {
        return $this->maxRows;
    }

    /**
     * @param int $maxRows
     *
     * @return $this
     */
    public function setMaxRows(int $maxRows)
    {
        $this->maxRows = $maxRows;

        return $this;
    }
}