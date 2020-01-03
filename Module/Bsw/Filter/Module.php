<?php

namespace Leon\BswBundle\Module\Bsw\Filter;

use Leon\BswBundle\Annotation\Entity\Filter;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Filter\Entity\Senior;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Module\Form\Form;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const QUERY             = 'Query';              // [全局配置] 列表查询
    const FILTER_ANNOTATION = 'FilterAnnotation';   // [全局配置] 注释补充或覆盖
    const FILTER_OPERATE    = 'FilterOperates';     // [全局配置] 操作按钮
    const FILTER_CONDITION  = 'FilterCondition';    // [全局配置] 条件处理

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'filter';
    }

    /**
     * @return string|null
     */
    public function twig(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function css(): ?array
    {
        return null;
    }

    /**
     * @return array
     */
    public function javascript(): ?array
    {
        return null;
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * Exception about annotation
     *
     * @param string $key
     *
     * @return string
     */
    protected function getAnnotationException(string $key): string
    {
        if ($this->entity) {
            return "@Filter() in key {$key}";
        }

        $annotation = self::FILTER_ANNOTATION;

        return "Item in {$this->method}{$annotation}():array {$key}";
    }

    /**
     * Get query options
     *
     * @return array
     * @throws
     */
    protected function getQueryOptions(): array
    {
        if ($this->query) {
            return $this->query;
        }

        $this->query = $this->caller($this->method, self::QUERY, Abs::T_ARRAY, []);
        if ($this->entity && !isset($this->query['alias'])) {
            $this->query['alias'] = Helper::tableNameToAlias($this->entity);
        }

        return $this->query;
    }

    /**
     * List entity fields
     *
     * @return array
     * @throws
     */
    protected function listEntityFields(): array
    {
        $entityList = [];

        if ($this->entity) {
            $entityList[$this->query['alias']] = $this->entity;
        }

        foreach (($this->query['join'] ?? []) as $alias => $item) {
            $entityList[$alias] = $item['entity'];
        }

        $annotation = [];
        $annotationFull = [];
        $entityList = array_filter(array_unique($entityList));

        foreach ($entityList as $alias => $entity) {
            $fields = $this->web->getFilterAnnotation($entity, $this->input->enum);
            foreach ($fields as $key => $item) {
                $fields[$key]['field'] = "{$alias}.{$item['field']}";
            }
            $annotationFull[$alias] = $fields;
        }

        if ($annotationFull) {
            $annotation = array_merge(...array_values($annotationFull));
        }

        return [$annotation, $annotationFull];
    }

    /**
     * Annotation extra item handler
     *
     * @param string $field
     * @param mixed  $item
     * @param array  $annotationFull
     *
     * @return array
     */
    protected function annotationExtraItemHandler(string $field, $item, array $annotationFull): array
    {
        if ($item === false) {
            return [$field, []];
        }

        if (!is_array($item)) {
            return [$field, $item];
        }

        if (!(isset($item['table']) && isset($item['field']))) {
            return [$field, $item];
        }

        $_table = Helper::dig($item, 'table');
        $_field = $item['field'];

        $item['field'] = "{$_table}.{$_field}";
        $_index = Helper::dig($item, 'index') ?? 0;
        $_field = "{$_field}_{$_index}";

        $clone = $annotationFull[$_table][$_field] ?? [];
        $item = empty($clone) ? [] : array_merge($clone, $item);

        return [$_field, $item];
    }

    /**
     * Annotation handler
     *
     * @return array
     * @throws
     */
    protected function handleAnnotation(): array
    {
        /**
         * filter annotation
         */

        [$annotation, $annotationFull] = $this->listEntityFields();

        /**
         * filter extra annotation
         */

        $fn = self::FILTER_ANNOTATION;
        $annotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, []);
        $annotationExtra = $this->tailor($this->method, $fn, Abs::T_ARRAY, $annotationExtra, $annotation);

        /**
         * annotation handler with extra
         */

        foreach ($annotationExtra as $field => $item) {

            [$field, $item] = $this->annotationExtraItemHandler($field, $item, $annotationFull);

            if (!is_array($item)) {
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if (empty($item)) {
                $annotation[$field]['show'] = false;
            }

            if (isset($annotation[$field])) {
                $item = array_merge($annotation[$field], $item);
            }

            $original = $this->web->annotation(Filter::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Filter($item)]);
            $annotation[$field] = (array)current($item[Filter::class]);
        }

        $allowFields = [];
        foreach ($annotationFull as $alias => $item) {
            $allowFields = array_merge($allowFields, array_column($item, 'field'));
        }

        $_annotation = [];
        foreach ($annotation as $key => $item) {
            if (!$this->query) {
                $_annotation[Helper::camelToUnder($key)] = $item;
                continue;
            }

            if (strpos($item['field'], '.') === false) {
                $item['field'] = "{$this->query['alias']}.{$item['field']}";
            }
            if (in_array($item['field'], $allowFields)) {
                $_annotation[Helper::camelToUnder($key)] = $item;
            }
        }

        $annotation = Helper::sortArray($_annotation, 'sort');

        /**
         * hooks
         */

        $hooks = [];
        foreach ($annotation as $field => $item) {

            foreach ($item['hook'] as $hook) {
                $hooks[$hook][] = $field;
            }

            if (!$item['show']) {
                unset($annotation[$field]);
            }
        }

        return [$annotation, $hooks];
    }

    /**
     * Get filter data
     *
     * @param array $annotation
     * @param array $hooks
     *
     * @return array
     * @throws
     */
    protected function getFilterData(array $annotation, array $hooks): array
    {
        $extraArgs = [
            '_acme' => ['scene' => $this->input->key],
        ];

        $filter = $this->web->getArgs($this->input->key) ?? [];
        $filter = Helper::numericValues($filter);
        $filter = $this->web->hooker($hooks, $filter, true, null, null, $extraArgs);

        $condition = [];
        foreach ($annotation as $key => $item) {
            if (!isset($filter[$key]) && !isset($item['value'])) {
                continue;
            }

            if (isset($filter[$key])) {
                $annotation[$key]['value'] = $filter[$key];
            }

            if (!$item['group']) {
                $condition[$item['field']] = [
                    'value'  => $annotation[$key]['value'],
                    'filter' => $item['filter'],
                ];
                continue;
            }

            if (!isset($condition[$item['field']])) {
                $condition[$item['field']] = ['value' => [], 'filter' => Senior::class];
            }
            $condition[$item['field']]['value'][] = $annotation[$key]['value'];
        }

        foreach ($hooks as $hook => $fields) {
            foreach ($fields as $index => $field) {
                if (!isset($filter[$field])) {
                    unset($hooks[$hook][$index]);
                }
            }
            if (empty($hooks[$hook])) {
                unset($hooks[$hook]);
            }
        }

        $annotationValue = Helper::arrayColumn($annotation, 'value');
        $annotationValue = $this->web->hooker($hooks, $annotationValue, false, null, null, $extraArgs);

        foreach ($annotation as $key => $item) {
            $annotation[$key]['value'] = $annotationValue[$key];
        }

        return [$annotation, $condition];
    }

    /**
     * Filter data handler
     *
     * @param array $filter
     *
     * @return array
     * @throws
     */
    protected
    function handleFilterData(
        array $filter
    ): array {
        $record = [];
        $format = [];

        foreach ($filter as $key => $item) {

            /**
             * @var Form $form
             */
            $form = $item['type'];
            $label = $item['label'];

            $form->setStyle($item['style']);
            $form->setKey($key);
            $form->setField($item['field']);

            if (isset($item['value'])) {
                $form->setValue($item['value']);
            }

            if (method_exists($form, 'setSize')) {
                $form->setSize(Form::SIZE_MIDDLE);
            }

            /**
             * extra enum
             */

            $item = $this->handleForEnum($item);

            $enumClass = Select::class;
            if (get_class($form) === $enumClass) {
                if (!is_array($item['enum'])) {
                    $exception = $this->getAnnotationException($key);
                    throw new AnnotationException(
                        "{$exception} option `enum` must configure when type is {$enumClass}"
                    );
                }

                /**
                 * @var Select $form
                 */
                $form->setEnum($this->web->enumLang($item['enum']));
            }

            if (Helper::extendClass($form, Datetime::class, true)) {

                /**
                 * @var Datetime $form
                 */
                $format[$key] = $form->getFormat();
            }

            if (!$form->getPlaceholder()) {
                $form->setPlaceholder($item['placeholder'] ?: $label);
            }

            $record[$key] = [
                'label'  => $item['trans'] ? $this->web->labelLang($label) : $label,
                'column' => $item['column'],
                'type'   => $form,
                'sort'   => $item['sort'],
                'group'  => $item['group'],
                'title'  => $item['title'],
            ];
        }

        $submit = new Button('Search', $this->input->route, 'b:icon-search');
        $submit->setAttributes(['bsw-method' => 'submit']);

        $operates = $this->caller($this->method, self::FILTER_OPERATE, Abs::T_ARRAY, []);
        $operates = array_merge(['search' => $submit], $operates);

        foreach ($operates as $operate) {

            $buttonCls = Button::class;
            if (!Helper::extendClass($operate, $buttonCls, true)) {
                $fn = self::FILTER_OPERATE;
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
            }

            /**
             * @var Button $operate
             */

            $operate->setClick('setUrlToForm');
            $operate->setScript(Html::scriptBuilder($operate->getClick(), $operate->getArgs()));

            try {
                $operate->setUrl($this->web->url($operate->getRoute(), $operate->getArgs(), false));
            } catch (RouteNotFoundException $e) {
                $this->input->logger->warning("Filter button route error, {$e->getMessage()}");
            }

            $operate->setHtmlType(Button::TYPE_SUBMIT);
            $operate->setSize(Button::SIZE_MIDDLE);
            $operate->setDisabled(!$this->web->routeIsAccess($operate->getRouteForAccess()));
        }

        return [$record, $operates, $format];
    }

    /**
     * Get show filter item list
     *
     * @param array $annotation
     * @param array $group
     * @param array $diffuse
     *
     * @return array
     */
    protected
    function getShowFilterItemList(
        array $annotation,
        array $group,
        array $diffuse
    ): array {
        $showList = Helper::arrayColumn($annotation, 'showPriority');
        foreach ($showList as $key => $priority) {
            if ($name = $diffuse[$key] ?? null) {
                if (!isset($showList[$name])) {
                    $showList[$name] = $priority;
                }
                unset($showList[$key]);
            }
        }

        if ($this->entity) {
            $document = $this->web->caching(
                function () {
                    $document = $this->web->mysqlSchemeDocument(Helper::tableNameFromCls($this->entity));

                    return Helper::arrayColumn($document['fields'], true, 'name');
                }
            );

            foreach ($showList as $key => &$priority) {
                $key = substr($key, 0, strrpos($key, '_'));
                $scheme = $document[$key] ?? null;
                if (empty($scheme)) {
                    continue;
                }

                if ($scheme['type'] === 'char') {
                    $priority += 3;
                } elseif (strpos($scheme['type'], 'int') !== false) {
                    $priority += 4;
                }

                if ($scheme['flag'] === 'PRI') {
                    $priority += 10;
                } elseif (!empty($scheme['flag'])) {
                    $priority += 5;
                }

                if (strpos($key, 'state') !== false) {
                    $priority += 1;
                }
            }
        }

        arsort($showList);

        return array_keys($showList);
    }

    /**
     * Get filter group
     *
     * @param array $annotation
     *
     * @return array
     */
    protected
    function getFilterGroup(
        array $annotation
    ): array {
        $group = [];
        $diffuse = [];

        foreach ($annotation as $field => $item) {
            if (!$item['group']) {
                continue;
            }
            $key = "{$item['group']}_group";
            $group[$key][] = $field;
            $diffuse[$field] = $key;
        }

        return [$group, $diffuse];
    }

    /**
     * Handler show list
     *
     * @param array  $annotation
     * @param Output $output
     */
    protected
    function handlerShowList(
        array $annotation,
        Output $output
    ) {
        if ($this->input->iframe) {
            $output->maxShow = ceil($output->maxShow / 2);
        }

        [$output->group, $output->diffuse] = $this->getFilterGroup($annotation);
        $output->showFull = $this->getShowFilterItemList($annotation, $output->group, $output->diffuse);
        $output->showList = array_slice($output->showFull, 0, $output->maxShow);

        $output->showFullJson = $this->json($output->showFull);
        $output->showListJson = $this->json($output->showList);
    }

    /**
     * Handler filter
     *
     * @param Output $output
     */
    protected
    function handlerFilter(
        Output $output
    ) {
        foreach ($output->group as $name => $members) {
            foreach ($members as $field) {
                if (!isset($output->filter[$name])) {
                    $output->filter[$name] = [
                        'label'  => $output->filter[$field]['label'],
                        'column' => $output->filter[$field]['column'],
                        'type'   => [],
                        'sort'   => $output->filter[$field]['sort'],
                        'group'  => $name,
                        'title'  => null,
                    ];
                }
                $output->filter[$name]['type'][] = $output->filter[$field]['type'];
                $output->filter[$name]['title'] = $output->filter[$field]['title'];
                unset($output->filter[$field]);
            }
        }

        $output->filter = Helper::sortArray($output->filter, 'sort');
    }

    /**
     * @return ArgsOutput
     * @throws
     */
    public
    function logic(): ArgsOutput
    {
        $output = new Output();

        /**
         * handle annotation
         */

        $this->getQueryOptions();
        [$annotation, $hooks] = $this->handleAnnotation();

        [$filter, $condition] = $this->getFilterData($annotation, $hooks);
        $condition = $this->caller(
            $this->method,
            self::FILTER_CONDITION,
            Abs::T_ARRAY,
            $condition,
            [$filter, $condition]
        );

        [$output->filter, $output->operates, $format] = $this->handleFilterData($filter);
        $output->condition = $condition;
        $output->formatJson = $this->json($format);

        $this->handlerShowList($annotation, $output);
        $this->handlerFilter($output);

        $output = $this->caller(
            $this->method . ucfirst($this->name()),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            [$output]
        );

        return $output;
    }
}