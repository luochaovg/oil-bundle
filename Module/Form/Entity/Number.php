<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Number extends Form
{
    use Size;
    use PreviewRoute;
    use ButtonLabel;

    /**
     * @var float
     */
    protected $step = 1.0;

    /**
     * @var float|int
     */
    protected $min = 0;

    /**
     * @var float|int
     */
    protected $max = Abs::MYSQL_INT_UNS_MAX;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setButtonLabel('Popup for select');
    }

    /**
     * @return float
     */
    public function getStep(): float
    {
        return $this->step;
    }

    /**
     * @param float $step
     */
    public function setStep(float $step): void
    {
        $this->step = $step;
    }

    /**
     * @return float|int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param $min
     *
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param $max
     *
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }
}