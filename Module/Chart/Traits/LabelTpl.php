<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait LabelTpl
{
    /**
     * @var string
     */
    protected $labelTpl = "\n{b}\n\n{hr|}\n\n{c} ({d}%)\n";

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