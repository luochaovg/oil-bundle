<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Form;

class Slider extends Form
{
    /**
     * @var bool
     */
    protected $dots = false;

    /**
     * @var int
     */
    protected $min = 0;

    /**
     * @var int
     */
    protected $max = 100;

    /**
     * @var array|int
     */
    protected $marks = [
        20 => 20,
        40 => 40,
        60 => 60,
        80 => 80,
    ];

    /**
     * @var bool
     */
    protected $included = true;

    /**
     * @var bool
     */
    protected $range = false;

    /**
     * @var int
     */
    protected $step = 1;

    /**
     * @var bool
     */
    protected $vertical = false;

    /**
     * @var bool|null
     */
    protected $tooltipVisible = null;

    /**
     * @var string
     */
    protected $tipFormatter = '(value) => `${value}%`';

    /**
     * @return bool
     */
    public function isDots(): bool
    {
        return $this->dots;
    }

    /**
     * @param bool $dots
     *
     * @return $this
     */
    public function setDots(bool $dots = true)
    {
        $this->dots = $dots;

        return $this;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     *
     * @return $this
     */
    public function setMin(int $min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     *
     * @return $this
     */
    public function setMax(int $max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarks(): string
    {
        return json_encode($this->marks, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $marks
     *
     * @return $this
     */
    public function setMarks(array $marks)
    {
        $this->marks = $marks;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncluded(): bool
    {
        return $this->included;
    }

    /**
     * @param bool $included
     *
     * @return $this
     */
    public function setIncluded(bool $included = true)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRange(): bool
    {
        return $this->range;
    }

    /**
     * @param bool $range
     *
     * @return $this
     */
    public function setRange(bool $range = true)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * @param int $step
     *
     * @return $this
     */
    public function setStep(int $step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVertical(): bool
    {
        return $this->vertical;
    }

    /**
     * @param bool $vertical
     *
     * @return $this
     */
    public function setVertical(bool $vertical = true)
    {
        $this->vertical = $vertical;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTooltipVisible(): ?bool
    {
        return $this->tooltipVisible;
    }

    /**
     * @param bool $tooltipVisible
     *
     * @return $this
     */
    public function setTooltipVisible(bool $tooltipVisible = true)
    {
        $this->tooltipVisible = $tooltipVisible;

        return $this;
    }

    /**
     * @return string
     */
    public function getTipFormatter(): string
    {
        return $this->tipFormatter;
    }

    /**
     * @param string $tipFormatter
     *
     * @return $this
     */
    public function setTipFormatter(string $tipFormatter)
    {
        $this->tipFormatter = $tipFormatter;

        return $this;
    }
}