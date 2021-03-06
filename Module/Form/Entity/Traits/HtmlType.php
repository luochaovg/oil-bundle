<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait HtmlType
{
    /**
     * @var string
     */
    protected $htmlType;

    /**
     * @return string
     */
    public function getHtmlType(): string
    {
        return $this->htmlType;
    }

    /**
     * @param string $htmlType
     *
     * @return $this
     */
    public function setHtmlType(string $htmlType)
    {
        $this->htmlType = $htmlType;

        return $this;
    }
}