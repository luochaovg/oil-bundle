<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait Style
{
    /**
     * @var array
     */
    protected $style = [];

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return Helper::jsonStringify($this->style);
    }

    /**
     * @return string|null
     */
    public function getStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->style);
    }

    /**
     * @param array $style
     *
     * @return $this
     */
    public function setStyle(array $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @param array $style
     *
     * @return $this
     */
    public function appendStyle(array $style)
    {
        $this->style = array_merge($this->style, $style);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasStyle(string $name): bool
    {
        return isset($this->style[$name]);
    }
}