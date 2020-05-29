<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait Feature
{
    /**
     * @var array
     */
    protected $feature = [
        'saveAsImage' => [
            'title'      => 'Save',
            'pixelRatio' => 2,
        ],
    ];

    /**
     * @return array
     */
    public function getFeature(): array
    {
        return $this->feature;
    }

    /**
     * @param array $feature
     *
     * @return $this
     */
    public function setFeature(array $feature)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setFeatureField(string $field, $value)
    {
        if (is_null($value)) {
            unset($this->feature[$field]);
        } else {
            $this->feature[$field] = $value;
        }

        return $this;
    }
}