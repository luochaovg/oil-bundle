<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait MapKey
{
    /**
     * @var string
     */
    protected $mapKey = 'china';

    /**
     * @return string
     */
    public function getMapKey(): string
    {
        return $this->mapKey;
    }

    /**
     * @param string $mapKey
     *
     * @return $this
     */
    public function setMapKey(string $mapKey)
    {
        $this->mapKey = $mapKey;

        return $this;
    }
}