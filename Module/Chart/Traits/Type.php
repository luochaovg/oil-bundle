<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Type
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }
}