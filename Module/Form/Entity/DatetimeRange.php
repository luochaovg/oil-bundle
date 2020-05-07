<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;

class DatetimeRange extends Datetime
{
    /**
     * @var string
     */
    protected $timeFormat = 'HH:mm:ss';

    /**
     * @var string
     */
    protected $timeHead = Abs::DAY_BEGIN;

    /**
     * @var string
     */
    protected $timeTail = Abs::DAY_END;

    /**
     * @var string
     */
    protected $separator = '~';

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'range-picker';
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        if (is_string($this->value)) {
            $this->value = explode(Abs::FORM_DATA_SPLIT, $this->value);
        }

        if (is_array($this->value) && count($this->value) >= 2) {
            return [trim($this->value[0]), trim($this->value[1])];
        }

        return [null, null];
    }

    /**
     * @return string
     */
    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    /**
     * @param string $timeFormat
     *
     * @return $this
     */
    public function setTimeFormat(string $timeFormat)
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeHead(): string
    {
        return $this->timeHead;
    }

    /**
     * @param string $timeHead
     *
     * @return $this
     */
    public function setTimeHead(string $timeHead)
    {
        $this->timeHead = $timeHead;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeTail(): string
    {
        return $this->timeTail;
    }

    /**
     * @param string $timeTail
     *
     * @return $this
     */
    public function setTimeTail(string $timeTail)
    {
        $this->timeTail = $timeTail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;

        return $this;
    }
}