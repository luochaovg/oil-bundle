<?php

namespace Leon\BswBundle\Module\Hook;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Exception\HookException;

abstract class Hook
{
    /**
     * @var mixed
     */
    private $item;

    /**
     * @var bool
     */
    private $object = true;

    /**
     * For preview
     *
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    abstract protected function preview($value, array $args, array $extraArgs = []);

    /**
     * For persistence
     *
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    abstract protected function persistence($value, array $args, array $extraArgs = []);

    /**
     * Hook
     *
     * @param mixed  $item
     * @param string $key
     * @param array  $args
     * @param bool   $persistence
     * @param array  $extraArgs
     *
     * @return mixed
     * @throws
     */
    public function hook($item, string $key, array $args = [], bool $persistence = false, array $extraArgs = [])
    {
        if (!is_object($item) && !is_array($item)) {
            throw new HookException('Hook item must be object or array');
        }

        $this->object = is_object($item);
        $this->item = (object)$item;

        if (!property_exists($this->item, $key)) {
            return $item;
        }

        $value = $this->item->{$key};
        $fn = $persistence ? 'persistence' : 'preview';

        // keep origin value
        if ($suffix = Helper::dig($extraArgs, '_suffix')) {
            $key = "{$key}{$suffix}";
        }

        // to hook
        $this->item->{$key} = $this->{$fn}($value, $args, $extraArgs);

        return $this->object ? $this->item : (array)$this->item;
    }
}