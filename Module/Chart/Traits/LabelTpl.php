<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait LabelTpl
{
    /**
     * @var string
     */
    protected $labelTpl = "{a|{a}}\n{hr|}\n {b|{b}：}{c}  {per|{d}%} ";

    /**
     * @return string
     */
    public function getLabelTpl(): string
    {
        return $this->labelTpl;
    }

    /**
     * @param string $labelTpl
     *
     * @return $this
     */
    public function setLabelTpl(string $labelTpl)
    {
        $this->labelTpl = $labelTpl;

        return $this;
    }
}