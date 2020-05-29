<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Title
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }
}