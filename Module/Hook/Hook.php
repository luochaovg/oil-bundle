<?php

namespace Leon\BswBundle\Module\Hook;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\HookException;

abstract class Hook
{
    /**
     * @var mixed
     */
    protected $item;

    /**
     * @var bool
     */
    protected $object = true;

    /**
     * @var string
     */
    protected $field;

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
        $this->field = $key;

        if (!property_exists($this->item, $this->field)) {
            return $item;
        }

        $value = $this->item->{$this->field};
        $fn = $persistence ? 'persistence' : 'preview';

        // keep origin value
        if ($suffix = Helper::dig($extraArgs, Abs::HOOKER_FLAG_ENUMS_SUFFIX)) {
            $this->field = "{$this->field}{$suffix}";
        }

        // to hook
        $this->item->{$this->field} = $this->{$fn}($value, $args, $extraArgs);

        return $this->object ? $this->item : (array)$this->item;
    }
}