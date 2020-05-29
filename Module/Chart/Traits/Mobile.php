<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Mobile
{
    /**
     * @var bool
     */
    protected $mobile = false;

    /**
     * @return bool
     */
    public function isMobile(): bool
    {
        return $this->mobile;
    }

    /**
     * @param bool $mobile
     *
     * @return $this
     */
    public function setMobile(bool $mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }
}