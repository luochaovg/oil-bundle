<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait Options
{
    /**
     * @var array|string
     */
    protected $options = [];

    /**
     * @return string
     */
    public function getOptions(): string
    {
        if (is_string($this->options)) {
            return $this->options;
        }

        return Helper::jsonStringify(Helper::stringValues($this->options));
    }

    /**
     * @return array
     */
    public function getOptionsArray(): array
    {
        if (is_string($this->options)) {
            return [];
        }

        return $this->options;
    }

    /**
     * @param array|string $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function appendOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }
}