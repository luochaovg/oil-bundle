<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use stdClass;

class Arguments extends stdClass
{
    /**
     * Set argument
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set(string $key, $value)
    {
        $this->{$key} = $value;

        return $this;
    }

    /**
     * Set any arguments
     *
     * @param array $target
     *
     * @return $this
     */
    public function setAny(array $target)
    {
        foreach ($target as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get argument
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->isset($key) ? $this->{$key} : null;
    }

    /**
     * Get any arguments
     *
     * @param array $keys
     * @param bool  $withKey
     *
     * @return array
     */
    public function getAny(array $keys, bool $withKey = false)
    {
        $target = [];
        foreach ($keys as $key) {
            if ($withKey) {
                $target[$key] = $this->get($key);
            } else {
                array_push($target, $this->get($key));
            }
        }

        return $target;
    }

    /**
     * Get all arguments
     *
     * @return array
     */
    public function getAll(): array
    {
        return Helper::entityToArray($this);
    }

    /**
     * Isset argument
     *
     * @param string $key
     *
     * @return bool
     */
    public function isset(string $key): bool
    {
        return isset($this->{$key});
    }

    /**
     * __getter
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}