<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait TipsTpl
{
    /**
     * @var string
     */
    protected $tipsTpl = "{a} <br/>{b}: {c} ({d}%)";

    /**
     * @return string
     */
    public function getTipsTpl(): string
    {
        return $this->tipsTpl;
    }

    /**
     * @param string $tipsTpl
     *
     * @return $this
     */
    public function setTipsTpl(string $tipsTpl)
    {
        $this->tipsTpl = $tipsTpl;

        return $this;
    }
}