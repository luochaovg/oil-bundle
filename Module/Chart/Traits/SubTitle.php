<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait SubTitle
{
    /**
     * @var string
     */
    protected $subTitle;

    /**
     * @return string
     */
    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    /**
     * @param string $subTitle
     *
     * @return $this
     */
    public function setSubTitle(string $subTitle)
    {
        $this->subTitle = $subTitle;

        return $this;
    }
}