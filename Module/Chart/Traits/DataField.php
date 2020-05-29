<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait DataField
{
    protected $dataField = [];

    /**
     * @return array
     */
    public function getDataField(): array
    {
        return $this->dataField;
    }

    /**
     * @param array $dataField
     *
     * @return $this
     */
    public function setDataField(array $dataField)
    {
        $this->dataField = $dataField;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setDataFieldField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->dataField[$field]);
        } else {
            $this->dataField[$field] = $value;
        }

        return $this;
    }
}