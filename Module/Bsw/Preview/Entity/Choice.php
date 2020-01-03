<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Entity;

class Choice
{
    /**
     * @var bool
     */
    private $enable = false;

    /**
     * @var bool
     */
    private $multiple = false;

    /**
     * @var array
     */
    private $fields = ['id'];

    /**
     * @var string
     */
    private $split = ',';

    /**
     * Choice constructor.
     *
     * @param bool|null $enable
     * @param bool|null $multiple
     */
    public function __construct(bool $enable = null, bool $multiple = null)
    {
        isset($enable) && $this->enable = $enable;
        isset($multiple) && $this->multiple = $multiple;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     *
     * @return $this
     */
    public function setEnable(bool $enable = true)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     *
     * @return $this
     */
    public function setMultiple(bool $multiple = true)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return string
     */
    public function getSplit(): string
    {
        return $this->split;
    }

    /**
     * @param string $split
     *
     * @return $this
     */
    public function setSplit(string $split)
    {
        $this->split = $split;

        return $this;
    }
}