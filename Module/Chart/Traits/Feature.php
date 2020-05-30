<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Component\Helper;

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
        Helper::setArrayValue($this->feature, $field, $value);

        return $this;
    }
}