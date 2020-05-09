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
use Leon\BswBundle\Module\Form\Entity\Input as FormInput;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const QUERY                  = 'Query';                 // [全局配置] 列表查询
    const FILTER_ANNOTATION      = 'FilterAnnotation';      // [全局配置] 注释补充或覆盖
    const FILTER_ANNOTATION_ONLY = 'FilterAnnotationOnly';  // [全局配置] 注释限制
    const FILTER_OPERATE         = 'FilterOperates';        // [全局配置] 操作按钮
    const FILTER_CORRECT         = 'FilterCorrect';         // [全局配置] 矫正条件

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
            if (is_string($item['entity'])) {
                $entityList[$alias] = $item['entity'];
            } elseif (is_array($item['entity']) && isset($item['entity']['from'])) {
                $entityList[$alias] = $item['entity']['from'];
            }
        }

        $filterAnnotation = [];
        $filterAnnotationFull = [];
        $entityList = array_filter(array_unique($entityList));

        $extraArgs = [
            'enumClass'      => $this->input->enum,
            'doctrinePrefix' => $this->web->parameter('doctrine_prefix'),
        ];

        foreach ($entityList as $alias => $entity) {
            $filterAnnotationFull[$alias] = $this->web->getFilterAnnotation($entity, $extraArgs);
            foreach ($filterAnnotationFull[$alias] as $key => &$item) {
                $item['field'] = "{$alias}.{$item['field']}";
            }
        }

        if ($filterAnnotationFull) {
            // $filterAnnotation = array_merge(...array_values(array_reverse($filterAnnotationFull)));
            $filterAnnotation = $filterAnnotationFull[$this->query['alias']];
        }

        return [$filterAnnotation, $filterAnnotationFull];
    }

    /**
     * Annotation extra item handler
     *
     * @param string $field
     * @param mixed  $item
     * @param array  $filterAnnotationFull
     *
     * @return array
     */
    protected function annotationExtraItemHandler(string $field, $item, array $filterAnnotationFull): array
    {
        if (is_bool($item)) {
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
        $field = "{$field}_{$_index}";

        $clone = $filterAnnotationFull[$_table][$_field] ?? [];
        $item = empty($clone) ? [] : array_merge($clone, $item);

        return [$field, $item];
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

        [$filterAnnotation, $filterAnnotationFull] = $this->listEntityFields();

        /**
         * preview annotation only
         */

        $fn = self::FILTER_ANNOTATION_ONLY;
        $filterAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, null);

        $arguments = $this->arguments(['target' => $filterAnnotationExtra], compact('filterAnnotation'));
        $filterAnnotationExtra = $this->tailor($this->method, $fn, Abs::T_ARRAY, $arguments);

        /**
         * extra annotation handler
         */

        if (!is_null($filterAnnotationExtra)) {

            $filterAnnotationOnlyKey = array_keys($filterAnnotationExtra);
            $filterAnnotation = Helper::arrayPull($filterAnnotation, $filterAnnotationOnlyKey);

        } else {

            /**
             * filter extra annotation
             */

            $fn = self::FILTER_ANNOTATION;
            $filterAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, []);
            $arguments = $this->arguments(['target' => $filterAnnotationExtra], compact('filterAnnotation'));
            $filterAnnotationExtra = $this->tailor($this->method, $fn, null, $arguments);
        }

        /**
         * annotation handler with extra
         */

        foreach ($filterAnnotationExtra as $field => $item) {

            $_item = $item;
            [$field, $item] = $this->annotationExtraItemHandler($field, $item, $filterAnnotationFull);

            if (!is_array($item)) {
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if ($_item === false) {
                $filterAnnotation[$field]['show'] = false;
            }

            if (isset($filterAnnotation[$field])) {
                $item = array_merge($filterAnnotation[$field], $item);
            }

            $original = $this->web->annotation(Filter::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Filter($item)]);
            $filterAnnotation[$field] = (array)current($item[Filter::class]);
        }

        $allowFields = [];
        foreach ($filterAnnotationFull as $alias => $item) {
            $allowFields = array_merge($allowFields, array_column($item, 'field'));
        }

        $_annotation = [];
        foreach ($filterAnnotation as $key => $item) {
            if (!$this->query) {
                $_annotation[Helper::camelToUnder($key)] = $item;
                continue;
            }

            $item['field'] = Helper::tableFieldAddAlias($item['field'], $this->query['alias']);
            if (in_array($item['field'], $allowFields)) {
                $_annotation[Helper::camelToUnder($key)] = $item;
            }
        }

        $filterAnnotation = Helper::sortArray($_annotation, 'sort');

        /**
         * hooks
         */
        $hooks = [];
        foreach ($filterAnnotation as $field => $item) {

            foreach ($item['hook'] as $hook) {
                $hooks[$hook][] = $field;
            }

            if (!$item['show']) {
                unset($filterAnnotation[$field]);
            }
        }

        return [$filterAnnotation, $hooks];
    }

    /**
     * Get filter data
     *
     * @param array $filterAnnotation
     * @param array $hooks
     *
     * @return array
     * @throws
     */
    protected function getFilterData(array $filterAnnotation, array $hooks): array
    {
        $extraArgs = [Abs::HOOKER_FLAG_ACME => ['scene' => 'filter']];

        $filter = $this->web->getArgs($this->input->key) ?? [];
        $filter = Helper::numericValues($filter);
        $filter = $this->web->hooker($hooks, $filter, true, null, null, $extraArgs);

        $condition = [];
        foreach ($filterAnnotation as $key => $item) {
            if (!isset($filter[$key]) && !isset($item['value'])) {
                continue;
            }

            if (isset($filter[$key])) {
                $filterAnnotation[$key]['value'] = $filter[$key];
            }

            if (!$item['group']) {
                $condition[$item['field']] = [
                    'value'  => $filterAnnotation[$key]['value'],
                    'filter' => $item['filter'],
                ];
                continue;
            }

            if (!isset($condition[$item['field']]) || is_scalar($condition[$item['field']]['value'])) {
                $condition[$item['field']] = ['value' => [], 'filter' => Senior::class];
            }

            $condition[$item['field']]['value'][] = $filterAnnotation[$key]['value'];
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

        $filterAnnotationValue = Helper::arrayColumn($filterAnnotation, 'value');
        $filterAnnotationValue = $this->web->hooker($hooks, $filterAnnotationValue, false, null, null, $extraArgs);

        foreach ($filterAnnotation as $key => $item) {
            $filterAnnotation[$key]['value'] = $filterAnnotationValue[$key];
        }

        return [$filterAnnotation, $condition];
    }

    /**
     * Filter data handler
     *
     * @param array $filter
     *
     * @return array
     * @throws
     */
    protected function handleFilterData(array $filter): array
    {
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

            if (get_class($form) === FormInput::class) {
                /**
                 * @var FormInput $form
                 */
                $form->setAllowClear();
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

        $search = new Button('Search', $this->input->route, $this->input->cnf->icon_search);
        $search->setAttributes(['bsw-method' => 'search']);

        $export = null;
        if ($this->input->scene === 'preview') {
            $export = new Button('Export', $this->input->route, $this->input->cnf->icon_export, Button::THEME_DEFAULT);
            $export->setAttributes(['bsw-method' => 'export']);
            $export->setRouteForAccess('app_export');
        }

        $operates = $this->caller($this->method, self::FILTER_OPERATE, Abs::T_ARRAY, []);
        $operates = array_merge(['search' => $search, 'export' => $export], $operates);
        $operates = array_filter($operates);

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
            $operate->setUrl($this->web->urlSafe($operate->getRoute(), $operate->getArgs(), 'Filter button'));

            $operate->setHtmlType(Button::TYPE_SUBMIT);
            $operate->setSize(Button::SIZE_MIDDLE);
            $operate->setDisabled(!$this->web->routeIsAccess($operate->getRouteForAccess()));
        }

        return [$record, $operates, $format];
    }

    /**
     * Get show filter item list
     *
     * @param array $filterAnnotation
     * @param array $group
     * @param array $diffuse
     *
     * @return array
     */
    protected function getShowFilterItemList(array $filterAnnotation, array $group, array $diffuse): array
    {
        $showList = Helper::arrayColumn($filterAnnotation, 'showPriority');
        foreach ($showList as $key => $priority) {
            if ($name = $diffuse[$key] ?? null) {
                if (!isset($showList[$name])) {
                    $showList[$name] = $priority;
                }
                unset($showList[$key]);
            }
        }

        if ($this->entity) {
            $document = $this->entityDocument();
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
     * @param array $filterAnnotation
     *
     * @return array
     */
    protected function getFilterGroup(array $filterAnnotation): array
    {
        $group = [];
        $diffuse = [];

        foreach ($filterAnnotation as $field => $item) {
            if (!$item['group']) {
                continue;
            }
            $key = "{$item['group']}__group";
            $group[$key][] = $field;
            $diffuse[$field] = $key;
        }

        return [$group, $diffuse];
    }

    /**
     * Handle show list
     *
     * @param array  $filterAnnotation
     * @param Output $output
     */
    protected function handleShowList(array $filterAnnotation, Output $output)
    {
        if ($this->input->iframe) {
            $output->maxShow = ceil($output->maxShow / 2);
        }

        [$output->group, $output->diffuse] = $this->getFilterGroup($filterAnnotation);
        $output->showFull = $this->getShowFilterItemList($filterAnnotation, $output->group, $output->diffuse);
        $output->showList = array_slice($output->showFull, 0, $output->maxShow);

        $output->showFullJson = Helper::jsonStringify($output->showFull, '{}');
        $output->showListJson = Helper::jsonStringify($output->showList, '{}');
    }

    /**
     * Handle filter
     *
     * @param Output $output
     */
    protected function handleFilter(Output $output)
    {
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
    public function logic(): ArgsOutput
    {
        $output = new Output();

        /**
         * handle annotation
         */

        $this->getQueryOptions();
        [$filterAnnotation, $hooks] = $this->handleAnnotation();

        [$filter, $condition] = $this->getFilterData($filterAnnotation, $hooks);
        [$filter, $condition] = $this->caller(
            $this->method,
            self::FILTER_CORRECT,
            Abs::T_ARRAY,
            [$filter, $condition],
            $this->arguments(compact('filter', 'condition'))
        );

        [$output->filter, $output->operates, $format] = $this->handleFilterData($filter);
        $output->condition = $condition;
        $output->formatJson = Helper::jsonStringify($format, '{}');

        $this->handleShowList($filterAnnotation, $output);
        $this->handleFilter($output);

        $output = $this->caller(
            $this->method . ucfirst($this->name()),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}