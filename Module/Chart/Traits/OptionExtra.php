<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait OptionExtra
{
    /**
     * @var array
     */
    protected $optionExtra = [];

    /**
     * @return array
     */
    public function getOptionExtra(): array
    {
        return $this->optionExtra;
    }

    /**
     * @param array $optionExtra
     *
     * @return $this
     */
    public function setOptionExtra(array $optionExtra)
    {
        $this->optionExtra = $optionExtra;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOptionExtraField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->optionExtra[$field]);
        } else {
            $this->optionExtra[$field] = $value;
        }

        return $this;
    }
}