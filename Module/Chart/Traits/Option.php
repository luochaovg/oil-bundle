<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Component\Helper;

trait Option
{
    /**
     * @var array
     */
    protected $option = [];

    /**
     * @return array
     */
    public function getOption(): array
    {
        return $this->option;
    }

    /**
     * @param array $option
     *
     * @return $this
     */
    public function setOption(array $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptionStringify(): string
    {
        return Helper::jsonStringify($this->option, '{}');
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOptionField(string $field, $value)
    {
        Helper::setArrayValue($this->option, $field, $value);

        return $this;
    }
}