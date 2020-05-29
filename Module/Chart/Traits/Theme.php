<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait Theme
{
    /**
     * @var string
     */
    protected $theme = Abs::CHART_DEFAULT_THEME;

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     *
     * @return $this
     */
    public function setTheme(string $theme)
    {
        $this->theme = $theme;

        return $this;
    }
}