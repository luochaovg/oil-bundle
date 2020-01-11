<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Repository\FoundationRepository;
use ReflectionClass;

abstract class Bsw
{
    /**
     * @const string
     */
    const ENTITY             = 'Entity';            // [全局配置] 表实体类
    const ANNOTATION         = 'Annotation';        // [全局配置] 注释补充或覆盖
    const TAILOR             = 'Tailor';            // [全局配置] 定制逻辑
    const ENUM_EXTRA         = 'EnumExtra';         // 额外枚举
    const ARGS_BEFORE_RENDER = 'ArgsBeforeRender';  // 渲染前处理(输出)

    /**
     * @var BswBackendController
     */
    protected $web;

    /**
     * @var ArgsInput
     */
    protected $input;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $tailor = [];

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var FoundationEntity
     */
    protected $entityInstance;

    /**
     * @var FoundationRepository
     */
    protected $repository;

    /**
     * Bsw constructor.
     *
     * @param BswBackendController $web
     */
    public function __construct(BswBackendController $web)
    {
        $this->web = $web;
    }

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    abstract public function name(): string;

    /**
     * @return string|null
     */
    abstract public function twig(): ?string;

    /**
     * @return array
     */
    abstract public function css(): ?array;

    /**
     * @return array
     */
    abstract public function javascript(): ?array;

    /**
     * @return ArgsInput
     */
    abstract public function input(): ArgsInput;

    /**
     * @return ArgsOutput
     */
    abstract public function logic(): ArgsOutput;

    /**
     * @param ArgsInput $input
     *
     * @throws
     */
    public function initialization(ArgsInput $input)
    {
        $this->input = $input;

        // class/method name
        $this->class = Helper::underToCamel($this->input->cls, false, '-');
        $this->method = Helper::underToCamel($this->input->fn, true, '-');

        // tailor
        $this->tailor = $this->caller($this->method, self::TAILOR, Abs::T_ARRAY, []);

        // entity
        $entityFlag = self::ENTITY;
        $method = $this->method . $entityFlag;

        $this->entity = $this->caller($this->method, $entityFlag, Abs::T_STRING);
        if (empty($this->entity)) {
            return;
        }

        if (!Helper::extendClass($this->entity, FoundationEntity::class)) {
            throw new ModuleException(
                "Method {$method}():string should be class and extend " . FoundationEntity::class
            );
        }

        // entity instance
        $this->entityInstance = new $this->entity;

        // repository
        $this->repository = $this->web->repo($this->entity);
    }

    /**
     * @return string
     */
    protected function method(): string
    {
        return "module" . ucfirst($this->name());
    }

    /**
     * Caller
     *
     * @param string $prefix
     * @param string $call
     * @param string $type
     * @param mixed  $default
     * @param array  $args
     *
     * @return mixed|null
     * @throws
     */
    protected function caller(string $prefix, string $call, string $type = null, $default = null, array $args = [])
    {
        if (!method_exists($this->web, $call = "{$prefix}{$call}")) {
            return $default;
        }

        $data = call_user_func_array([$this->web, $call], $args) ?? $default;

        if ($type) {
            if (class_exists($type)) {
                $method = get_class($this->web) . "::{$call}():{$type}";
                Helper::objectInstanceOf($data, $type, $method);
            } else {
                $method = get_class($this->web) . "::{$call}():" . strtolower($type);
                Helper::callReturnType($data, $type, $method);
            }
        }

        return $data;
    }

    /**
     * Tailor handler
     *
     * @param string $prefix
     * @param string $call
     * @param string $type
     * @param mixed  ...$args
     *
     * @return mixed
     * @throws
     */
    protected function tailor(string $prefix, string $call, string $type = null, ...$args)
    {
        $argument = current($args) ?? null;
        $method = "{$prefix}{$call}";

        if (empty($this->tailor)) {
            return $argument;
        }

        foreach ($this->tailor as $class => $fields) {

            $fn = self::TAILOR;

            // check tailor return keys
            if (!Helper::extendClass($class, Tailor::class)) {
                $tailorClass = Tailor::class;
                throw new ModuleException(
                    "{$this->class}::{$this->method}{$fn}() return must be array with {$tailorClass} key"
                );
            }

            // check tailor return values
            if (!is_array($fields)) {
                throw new ModuleException(
                    "{$this->class}::{$this->method}{$fn}() return must be array with array value"
                );
            }

            if (!method_exists($class, $method)) {
                continue;
            }

            foreach ($fields as $field) {

                // check tailor return fields
                foreach ((array)$field as $f) {
                    if (!property_exists($this->entityInstance, $f)) {
                        throw new ModuleException(
                            "Field `{$f}` don't exists in {$this->entity} when {$this->class}::{$this->method}{$fn}() returned"
                        );
                    }
                }

                $tailor = new $class($this->web, $field);
                $argument = call_user_func_array([$tailor, $method], $args) ?? $argument;
                $args[0] = $argument;

                // check type
                if ($type) {
                    if (class_exists($type)) {
                        $_method = get_class($tailor) . "::{$method}():{$type}";
                        Helper::objectInstanceOf($argument, $type, $_method);
                    } else {
                        $_method = get_class($tailor) . "::{$method}():" . strtolower($type);
                        Helper::callReturnType($argument, $type, $_method);
                    }
                }
            }
        }

        return $argument;
    }

    /**
     * @param string $tpl
     * @param string $field
     * @param array  $var
     * @param string $container
     *
     * @return string
     * @throws
     */
    protected function parseSlot(string $tpl, string $field, array $var = [], string $container = null): string
    {
        static $constants;

        /**
         * constants variable
         */

        if (!isset($constants)) {
            $_constants = (new ReflectionClass(Abs::class))->getConstants();
            foreach ($_constants as $key => $value) {
                if (strpos($key, 'TPL_') === 0 || strpos($key, 'SLOT_') === 0) {
                    $constants["Abs::{$key}"] = $value;
                }
            }
        }

        /**
         * custom variables
         */

        $variables = array_merge(
            $constants,
            [
                'slot'          => $field,
                'slot-scope'    => Abs::SLOT_VARIABLES,
                'field'         => $field,
                ':value'        => 'value',
                'value'         => '{{ value }}',
                'Abs::NIL'      => Abs::NIL,
                'Abs::DIRTY'    => Abs::DIRTY,
                'Abs::NOT_SET'  => Abs::NOT_SET,
                'Abs::NOT_FILE' => Abs::NOT_FILE,
                'Abs::SECRET'   => Abs::SECRET,
            ],
            $var
        );

        /**
         * out container tpl
         */

        $template = $tpl;
        if ($container) {
            $template = str_replace('{tpl}', $template, $container);
        }

        /**
         * parse
         */

        foreach ($variables as $key => $value) {
            $find = "{{$key}}";
            if (strpos($value, $find) !== false) {
                throw new ModuleException(
                    "Slot variable doesn't seem right, is looks like replace `{$find}` use `{$value}`"
                );
            }
            $template = str_replace($find, $value, $template);
        }

        if ($tpl == $template) {
            return $template;
        }

        return $this->parseSlot($template, $field, $var);
    }

    /**
     * Enum handler
     *
     * @param array $item
     * @param array $args
     *
     * @return array
     */
    protected function handleForEnum(array $item, array $args = []): array
    {
        return $this->web->caching(
            function () use ($item, $args) {

                /**
                 * extra enum
                 */
                if (is_string($item['enumExtra'])) {

                    $method = self::ENUM_EXTRA . ucfirst($item['enumExtra']);
                    $enum = (array)$item['enum'];

                    $enumExtra = $this->caller('acme', $method, Abs::T_ARRAY, [], array_merge([$enum], $args));
                    $enumExtra = $this->caller(
                        $this->method,
                        $method,
                        Abs::T_ARRAY,
                        $enumExtra,
                        array_merge([$enumExtra], $args)
                    );

                    if (isset($enumExtra)) {
                        $item['enum'] = $enumExtra + $enum;
                    }
                }

                if ($item['enum'] && $item['enumHandler']) {
                    $item['enum'] = call_user_func_array($item['enumHandler'], [$item['enum']]);
                    Helper::callReturnType($item['enum'], Abs::T_ARRAY);
                }

                return $item;
            }
        );
    }

    /**
     * Show error
     *
     * @param string $message
     * @param array  $args
     * @param string $route
     *
     * @return ArgsOutput
     */
    public function showError(string $message, array $args = [], string $route = null): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = (new Message($message))
            ->setClassify(Abs::TAG_CLASSIFY_ERROR)
            ->setRoute($route)
            ->setArgs($args);

        return $output;
    }

    /**
     * Show success
     *
     * @param string $message
     * @param array  $args
     * @param string $route
     *
     * @return ArgsOutput
     */
    public function showSuccess(string $message, array $args = [], ?string $route = ''): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = (new Message($message))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS)
            ->setRoute($route)
            ->setArgs($args);

        return $output;
    }

    /**
     * Show message
     *
     * @param Message $message
     *
     * @return ArgsOutput
     */
    public function showMessage(Message $message): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = $message;

        return $output;
    }

    /**
     * Json the array
     *
     * @param array $source
     *
     * @return string
     */
    public function json(array $source): string
    {
        return json_encode($source, JSON_UNESCAPED_UNICODE) ?: '{}';
    }
}