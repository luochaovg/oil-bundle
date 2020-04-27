<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Datetime extends Form
{
    use Size;
    use AllowClear;

    /**
     * @var string
     */
    protected $format = 'YYYY-MM-DD HH:mm:ss';

    /**
     * @var bool
     */
    protected $showTime = true;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'date-picker';
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat(string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowTime(): bool
    {
        return $this->showTime;
    }

    /**
     * @param bool $showTime
     *
     * @return $this
     */
    public function setShowTime(bool $showTime)
    {
        $this->showTime = $showTime;

        return $this;
    }
}