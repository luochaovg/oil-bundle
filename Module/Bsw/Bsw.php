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
    const ENTITY              = 'Entity';
    const QUERY               = 'Query';
    const ANNOTATION          = 'Annotation';
    const ANNOTATION_ONLY     = 'AnnotationOnly';
    const TAILOR              = 'Tailor';
    const ENUM_EXTRA          = 'EnumExtra';
    const INPUT_ARGS_HANDLER  = 'InputArgsHandler';
    const OUTPUT_ARGS_HANDLER = 'OutputArgsHandler';

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
     * @var string
     */
    protected $methodTailorBasic = 'tailor';

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
     * @throws
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

        $this->input = $this->caller(
            $this->method,
            self::INPUT_ARGS_HANDLER,
            $this->input(),
            $this->input,
            $this->arguments(['input' => $this->input])
        );

        // tailor
        $this->tailor = $this->caller($this->method, self::TAILOR, Abs::T_ARRAY, []);

        // entity
        $entityFlag = self::ENTITY;
        $method = $this->method . $entityFlag;

        $this->entity = $this->input->entity ?? $this->caller($this->method, $entityFlag, Abs::T_STRING);
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
        return "module" . Helper::underToCamel($this->name(), false);
    }

    /**
     * Set arguments
     *
     * @param array ...$target
     *
     * @return Arguments
     */
    protected function arguments(array ...$target): Arguments
    {
        return (new Arguments())->setMany(array_merge(...$target));
    }

    /**
     * Caller
     *
     * @param string    $prefix
     * @param string    $call
     * @param mixed     $type
     * @param mixed     $default
     * @param Arguments $args
     *
     * @return mixed|null
     * @throws
     */
    protected function caller(string $prefix, string $call, $type = null, $default = null, Arguments $args = null)
    {
        if (!method_exists($this->web, $call = "{$prefix}{$call}")) {
            return $default;
        }

        $args = $args ? [$args] : [];
        $data = call_user_func_array([$this->web, $call], $args) ?? $default;
        if ($type) {
            $type = (array)$type;
            $method = get_class($this->web) . "::{$call}():" . Helper::printArray($type, '[%s]', '');
            Helper::callReturnType($data, $type, $method);
        }

        return $data;
    }

    /**
     * Tailor handler
     *
     * @param string    $prefix
     * @param string    $call
     * @param mixed     $type
     * @param Arguments $args
     *
     * @return mixed
     * @throws
     */
    protected function tailor(string $prefix, string $call, $type = null, Arguments $args = null)
    {
        $argument = $args->target;
        $method = "{$prefix}{$call}";

        if (empty($this->tailor)) {
            return $args->default ?? $argument;
        }

        /**
         * Tailor core
         *
         * @param string $class
         * @param string $method
         * @param mixed  $field
         *
         * @return array
         */
        $tailorCore = function (string $class, string $method, $field) use ($type, &$args, $argument) {

            $tailor = new $class($this->web, $field);
            $_args = $args ? [$args] : [];
            $argument = call_user_func_array([$tailor, $method], $_args) ?? $argument;
            $args->target = $argument;

            if ($type) {
                $type = (array)$type;
                $method = get_class($tailor) . "::{$method}():" . Helper::printArray($type, '[%s]', '');
                Helper::callReturnType($argument, $type, $method);
            }

            return $argument;
        };

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
            if (!is_array($fields) && ($fields !== true)) {
                throw new ModuleException(
                    "{$this->class}::{$this->method}{$fn}() return must be array with array/true value"
                );
            }

            if (!method_exists($class, $method)) {
                continue;
            }

            if ($fields === true) {
                $argument = $tailorCore($class, $method, true);
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
                $argument = $tailorCore($class, $method, $field);
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
            $beginWith = [
                'NIL',
                'DIRTY',
                'NOT_SET',
                'NOT_FILE',
                'SECRET',
                'UNKNOWN',
                'UNALLOCATED',
                'COMMON',
                'TPL_',
                'SLOT_',
            ];
            foreach ($_constants as $key => $value) {
                foreach ($beginWith as $target) {
                    if (strpos($key, $target) === 0) {
                        $constants["Abs::{$key}"] = $value;
                    }
                }
            }
        }

        /**
         * custom variables
         */

        $variables = array_merge(
            $constants,
            [
                'uuid'   => "__{$field}",
                ':value' => 'value',
                'value'  => '{{ value }}',
                'title'  => $var['title'] ?? null,
                'field'  => Helper::camelToUnder($field, '-'),
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
        if (is_string($item['enumExtra'])) {
            $method = self::ENUM_EXTRA . ucfirst($item['enumExtra']);
            $enum = (array)$item['enum'];

            $arguments = $this->arguments(compact('enum'), $args);
            $enumExtra = $this->caller('acme', $method, Abs::T_ARRAY, [], $arguments);

            $arguments = $this->arguments(compact('enumExtra', 'enum'), $args);
            $enumExtra = $this->caller($this->method, $method, Abs::T_ARRAY, $enumExtra, $arguments);

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

    /**
     * Show error
     *
     * @param string $message
     * @param int    $code
     * @param array  $args
     * @param string $route
     *
     * @return ArgsOutput
     */
    public function showError(string $message, int $code = 0, array $args = [], string $route = null): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = (new Message($message))
            ->setClassify(Abs::TAG_CLASSIFY_ERROR)
            ->setRoute($route)
            ->setCode($code)
            ->setArgs($args);

        return $output;
    }

    /**
     * Show success
     *
     * @param string $message
     * @param array  $sets
     * @param array  $args
     * @param string $route
     *
     * @return ArgsOutput
     */
    public function showSuccess(string $message, array $sets = [], array $args = [], ?string $route = ''): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = (new Message($message))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS)
            ->setRoute($route)
            ->setSets($sets)
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
     * @return array
     */
    public function entityDocument(): array
    {
        return $this->web->caching(
            function () {
                $table = Helper::tableNameFromCls($this->entity);
                $document = $this->web->mysqlSchemeDocument($table);
                if (empty($document)) {
                    return [];
                }

                return Helper::arrayColumn($document['fields'], true, 'name');
            },
            "bsw-entity-{$this->entity}"
        );
    }
}