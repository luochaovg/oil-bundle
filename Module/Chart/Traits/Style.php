<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Component\Html;

trait Style
{
    /**
     * @var array
     */
    protected $style = [
        'margin' => '50px auto',
        'float' => 'none'
    ];

    /**
     * @return array
     */
    public function getStyle(): array
    {
        return $this->style;
    }

    /**
     * @return string
     */
    public function getStyleStringify(): string
    {
        return Html::cssStyleFromArray($this->getStyle());
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
     * @param string $field
     * @param mixed $value
     *
     * @return $this
     */
    public function setStyleField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->style[$field]);
        } else {
            $this->style[$field] = $value;
        }

        return $this;
    }
}