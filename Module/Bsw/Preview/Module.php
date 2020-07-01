<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Annotation\Entity\Preview;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Repository\FoundationRepository;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const BEFORE_HOOK   = 'BeforeHook';
    const AFTER_HOOK    = 'AfterHook';
    const CHOICE        = 'Choice';
    const BEFORE_RENDER = 'BeforeRender';
    const CHARM         = 'Charm';
    const OPERATES      = 'RecordOperates';
    const MIXED_HANDLER = 'MixedHandler';

    /**
     * @const string
     */
    const DRESS_DEFAULT = 'default';

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var string
     */
    protected $methodTailor = 'tailorPreview';

    /**
     * @var array
     */
    protected $long2sort = [
        Abs::SORT_ASC_LONG  => Abs::SORT_ASC,
        Abs::SORT_DESC_LONG => Abs::SORT_DESC,
    ];

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
        return 'preview';
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
     * Exception for annotation
     *
     * @param string $field
     *
     * @return string
     */
    protected function getAnnotationException(string $field): string
    {
        if ($this->entity) {
            return "@Preview() in {$this->entity}::{$field}";
        }

        $annotation = self::ANNOTATION;

        return "Item in {$this->method}{$annotation}():array {$field}";
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

        $query = $this->web->parseFilter($this->input->condition);
        $this->query = Helper::merge($this->query, $query);

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

        $previewAnnotation = $previewAnnotationFull = [];
        $mixedAnnotation = $mixedAnnotationFull = [];
        $entityList = array_filter(array_unique($entityList));

        $extraArgs = [
            'enumClass'          => $this->input->enum,
            'doctrinePrefix'     => $this->web->parameter('doctrine_prefix'),
            'doctrinePrefixMode' => $this->web->parameter('doctrine_prefix_mode'),
        ];

        foreach ($entityList as $alias => $entity) {
            $previewAnnotationFull[$alias] = $this->web->getPreviewAnnotation($entity, $extraArgs);
            $mixedAnnotationFull[$alias] = $this->web->getMixedAnnotation($entity, $extraArgs);
            foreach ($mixedAnnotationFull[$alias] as $key => &$item) {
                $item['field'] = "{$alias}.{$item['field']}";
            }
        }

        if ($previewAnnotationFull) {
            $previewAnnotation = array_merge(...array_values(array_reverse($previewAnnotationFull)));
        }

        if ($mixedAnnotationFull) {
            $mixedAnnotation = array_merge(...array_values(array_reverse($mixedAnnotationFull)));
        }

        return [$previewAnnotation, $previewAnnotationFull, $mixedAnnotation, $mixedAnnotationFull];
    }

    /**
     * Annotation extra item handler
     *
     * @param string $field
     * @param mixed  $item
     * @param array  $previewAnnotationFull
     *
     * @return array
     */
    protected function annotationExtraItemHandler(string $field, $item, array $previewAnnotationFull): array
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
        $_field = Helper::dig($item, 'field');

        $_item = $previewAnnotationFull[$_table][$_field] ?? [];
        $item = array_merge($_item, $item);

        return [$field, $item];
    }

    /**
     * Create slot template
     *
     * @param string $field
     * @param array  $item
     *
     * @return string|false
     * @throws
     */
    protected function createSlot(string $field, array $item)
    {
        /**
         * extra enum
         */

        $item = $this->handleForEnum($item, ['scene' => $this->input->scene]);

        /**
         * eradicate xss
         */

        foreach (['enum', 'enumExtra'] as $name) {
            if (is_array($item[$name])) {
                $item[$name] = Html::cleanArrayHtml($item[$name]);
            } elseif (!empty($item[$name])) {
                $item[$name] = Html::cleanHtml($item[$name]);
            }
        }

        /**
         * html content
         */

        if ($item['html'] === true) {
            return $this->parseSlot(Abs::SLOT_HTML_CONTAINER, $field);
        }

        /**
         * dress handler
         */

        if (isset($item['dress']) && !$item['status']) {
            if (is_string($item['dress']) && $item['dress'] === self::DRESS_DEFAULT) {
                $item['dress'] = '';
            }
            if (is_array($item['dress'])) {
                $item['dress'] = array_filter(
                    $item['dress'],
                    function ($v) {
                        return !(empty($v) || ($v === self::DRESS_DEFAULT));
                    }
                );
            }
        }

        /**
         * text use dress (dress type be string)
         */

        if (isset($item['dress']) && !$item['enum']) {

            if (!is_string($item['dress'])) {
                $exception = $this->getAnnotationException($field);
                throw new AnnotationException(
                    "{$exception} option `dress` should be string when not enum"
                );
            }

            $var = [
                'dress' => $item['dress'],
            ];

            return $this->parseSlot(Abs::TPL_SCALAR_DRESS, $field, $var, Abs::SLOT_CONTAINER);
        }

        /**
         * choice list (enum) use dress (dress type be sting or array)
         */

        if (isset($item['dress']) && $item['enum']) {

            $dressArray = false;
            if (is_array($item['dress'])) {
                $dressArray = true;
                $dressStringify = Helper::jsonStringify($item['dress']);
                $item['dress'] = "{$dressStringify}[value]";
            }

            $enumStringify = $this->web->enumLang($item['enum'], true);

            $var = [
                'Abs::SLOT_NOT_BLANK' => "{$enumStringify}[value]",
                'enum'                => "{$enumStringify}[value]",
                'dress'               => "{$item['dress']}",
            ];

            if ($item['status']) {
                $tpl = Abs::TPL_ENUM_STATUS_DRESS;
            } else {
                $tpl = $dressArray ? Abs::TPL_ENUM_MANY_DRESS : Abs::TPL_ENUM_ONE_DRESS;
                $var['value'] = "{{ {$enumStringify}[value] }}";
            }

            return $this->parseSlot($tpl, $field, $var, Abs::SLOT_CONTAINER);
        }

        /**
         * choice list (enum) without dress
         */

        if (!isset($item['dress']) && $item['enum']) {

            $enumStringify = $this->web->enumLang($item['enum'], true);
            $var = [
                'Abs::SLOT_NOT_BLANK' => "{$enumStringify}[value]",
                'value'               => "{{ {$enumStringify}[value] }}",
            ];

            return $this->parseSlot(Abs::TPL_ENUM_WITHOUT_DRESS, $field, $var, Abs::SLOT_CONTAINER);
        }

        /**
         * text use render (that slot)
         */

        if ($render = $item['render']) {
            if (Helper::strEndWith($render, Abs::HTML_SUFFIX)) {
                $render = $this->web->caching(
                    function () use ($render) {
                        return $this->web->renderPart($render);
                    }
                );
            }

            return $this->parseSlot($render, $field, [], Abs::SLOT_CONTAINER);
        }

        return false;
    }

    /**
     * Annotation handler
     *
     * @param Output $output
     *
     * @return array
     * @throws
     */
    protected function handleAnnotation(Output $output): array
    {
        /**
         * preview annotation
         */

        [$previewAnnotation, $previewAnnotationFull, $mixedAnnotation] = $this->listEntityFields();

        /**
         * preview annotation only
         */

        $fn = self::ANNOTATION_ONLY;
        $operate = Abs::TR_ACT;

        $previewAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, null);

        $arguments = $this->arguments(['target' => $previewAnnotationExtra], compact('previewAnnotation'));
        $previewAnnotationExtra = $this->tailor($this->methodTailor, $fn, [Abs::T_ARRAY, null], $arguments);

        /**
         * extra annotation handler
         */

        if (!is_null($previewAnnotationExtra)) {

            $previewAnnotationOnlyKey = array_keys($previewAnnotationExtra);
            $previewAnnotation = Helper::arrayPull($previewAnnotation, $previewAnnotationOnlyKey);

        } else {

            /**
             * preview extra annotation
             */

            $fn = self::ANNOTATION;

            $previewAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, []);
            if (!isset($previewAnnotationExtra[$operate])) {
                $previewAnnotationExtra[$operate] = ['show' => true];
            }

            $arguments = $this->arguments(['target' => $previewAnnotationExtra], compact('previewAnnotation'));
            $previewAnnotationExtra = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $arguments);
        }

        /**
         * annotation handler with extra
         */

        foreach ($previewAnnotationExtra as $field => $item) {

            $_item = $item;
            [$field, $item] = $this->annotationExtraItemHandler($field, $item, $previewAnnotationFull);

            if (!is_array($item)) {
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if ($_item === false) {
                $previewAnnotation[$field]['show'] = false;
            }

            if (isset($previewAnnotation[$field])) {
                $item = array_merge($previewAnnotation[$field], $item);
            }

            $original = $this->web->annotation(Preview::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Preview($item)]);
            $previewAnnotation[$field] = (array)current($item[Preview::class]);
        }

        $previewAnnotation = Helper::sortArray($previewAnnotation, 'sort');

        /**
         * mixed annotation handler
         */
        $arguments = $this->arguments(['mixed' => $mixedAnnotation]);
        $mixedAnnotation = $this->caller(
            $this->method,
            self::MIXED_HANDLER,
            Abs::T_ARRAY,
            $mixedAnnotation,
            $arguments
        );

        /**
         * hooks & columns
         */

        $scrollX = 0;
        $hooks = $columns = $slots = [];

        foreach ($previewAnnotation as $field => $item) {

            foreach ($item['hook'] as $k => $v) {
                if (is_numeric($k) && class_exists($v)) {
                    $hook = $v;
                    $hookArgs = [];
                } elseif (class_exists($k) && is_array($v)) {
                    $hook = $k;
                    $hookArgs = $v;
                } else {
                    continue;
                }
                if (isset($hook) && isset($hookArgs)) {
                    $hooks[$hook]['fields'][] = $field;
                    $hooks[$hook]['args'] = $hookArgs;
                }
            }

            if (!$item['show']) {
                continue;
            }

            $column = [
                'title'     => $this->web->fieldLang($item['label']),
                'dataIndex' => $field,
                'fixed'     => $item['fixed'],
            ];

            $pk = $this->entity ? $this->repository->pk() : Abs::PK;
            if ($field === $pk && is_null($column['fixed'])) {
                $column['fixed'] = 'left';
            }

            if ($field === $operate) {
                if (is_null($column['fixed'])) {
                    $column['fixed'] = 'right';
                }
                if (!isset($previewAnnotationExtra[$operate]['width'])) {
                    unset($item['width']);
                }
            }

            if ($width = ($item['width'] ?? false)) {
                $column['width'] = $width;
                $scrollX += $width;
            }

            if ($align = $item['align']) {
                $column['align'] = $align;
            }

            /**
             * slot handler
             */

            $slot = $this->createSlot($field, $item);

            if ($slot !== false) {
                $column['scopedSlots'] = ['customRender' => "__{$field}"];
                $slots[$field] = $slot;
            }

            /**
             * sorter
             */

            if ($mixed = $mixedAnnotation[$field] ?? null) {
                foreach ([Abs::ORDER, Abs::SORT] as $keyword) {

                    if (!$mixed[$keyword]) {
                        continue;
                    }

                    $column['sorter'] = true;
                    $column['sortDirections'] = $mixed["{$keyword}Directions"];
                }
            }

            $columns[$field] = $column;
        }

        $output->scrollX = $scrollX;
        $output->slots = $slots;
        $output->columns = $columns;

        return [$hooks, $previewAnnotation, $mixedAnnotation];
    }

    /**
     * When data is manual
     *
     * @param array $query
     *
     * @return array
     * @throws
     */
    protected function manualLister(array $query): array
    {
        if (!is_array($this->input->preview)) {
            throw new ModuleException('Given preview data if lister from controller');
        }

        return $this->web->manualListForPagination($this->input->preview, $query);
    }

    /**
     * Get preview data
     *
     * @param Output $output
     * @param array  $mixedAnnotation
     *
     * @return array|ArgsOutput
     * @throws ModuleException
     */
    protected function getPreviewData(Output $output, array $mixedAnnotation)
    {
        /**
         * sequence for order
         */

        if ($mixedAnnotation) {

            $sequence = $this->web->getArgs('sequence');
            $sequence = Helper::keyUnderToCamel($sequence ?? []);

            foreach ($sequence as $key => $direction) {
                if (!isset($mixedAnnotation[$key])) {
                    continue;
                }

                if (!in_array($direction, array_keys($this->long2sort))) {
                    continue;
                }

                $mixed = $mixedAnnotation[$key];
                $made = $mixed[Abs::ORDER] ? Abs::ORDER : Abs::SORT;
                $unmade = $mixed[Abs::ORDER] ? Abs::SORT : Abs::ORDER;

                $this->query[$made] = [$mixed['field'] => $this->long2sort[$direction]];
                unset($this->query[$unmade]);
                break;
            }

            $sequence = array_merge(
                $this->query[Abs::ORDER] ?? [],
                $this->query[Abs::SORT] ?? []
            );

            if ($sequence) {
                $key = Helper::tableFieldDelAlias(key($sequence));
                $output->columns[$key]['defaultSortOrder'] = array_flip($this->long2sort)[current($sequence)];
            }
        }

        /**
         * list by query
         */

        $page = intval($this->web->getArgs(Abs::PG_PAGE) ?? 1);
        $limit = intval($this->web->getArgs(Abs::PG_PAGE_SIZE));
        $limit = in_array($limit, Abs::PG_PAGE_SIZE_OPTIONS) ? $limit : FoundationRepository::PAGE_SIZE;

        $query = array_merge(
            [
                'paging' => true,
                'page'   => $page,
                'limit'  => $limit,
            ],
            $this->query
        );

        /**
         * Fetch data
         */

        if ($this->entity) {

            if (!isset($query[Abs::ORDER])) {
                $pk = $this->repository->pk();
                $query[Abs::ORDER] = ["{$query['alias']}.{$pk}" => Abs::SORT_DESC];
            }

            $arguments = $this->arguments(['target' => $query]);
            $query = $this->tailor($this->methodTailor, self::QUERY, Abs::T_ARRAY, $arguments);
            if ($this->web->getArgs('scene') === 'export') {
                return $this->showMessage(
                    (new Message())->setSets(
                        [
                            'entity' => base64_encode($this->entity),
                            'query'  => Helper::objectToString($query),
                        ]
                    )->setSignature()
                );
            }

            $list = $this->repository->lister($query);

        } else {
            $arguments = $this->arguments(['target' => $query]);
            $query = $this->tailor($this->methodTailor, self::QUERY, Abs::T_ARRAY, $arguments);
            $list = $this->manualLister($query);
        }

        $output->query = $query;

        /**
         * pagination
         */

        if ($query['paging']) {

            $page = $list;
            $list = Helper::dig($page, Abs::PG_ITEMS);
            $output->page = [
                'currentPage' => $page[Abs::PG_CURRENT_PAGE],
                'pageSize'    => $page[Abs::PG_PAGE_SIZE],
                'totalPage'   => $page[Abs::PG_TOTAL_PAGE],
                'totalItem'   => $page[Abs::PG_TOTAL_ITEM],
            ];
        }

        return $list;
    }

    /**
     * Preview data handler
     *
     * @param array  $list
     * @param array  $hooks
     * @param array  $previewAnnotation
     * @param Output $output
     *
     * @return array
     * @throws
     */
    protected function handlePreviewData(array $list, array $hooks, array $previewAnnotation, Output $output): array
    {
        $basicNumber = ($output->query['page'] - 1) * $output->query['limit'] + 1;

        /**
         * before hook (row record)
         *
         * @param array $original
         * @param array $extraArgs
         * @param int   $index
         *
         * @return mixed
         */
        $before = function (array $original, array $extraArgs, int $index) use ($basicNumber) {

            $number = $basicNumber + $index;
            if (method_exists($this->web, $fn = $this->method . self::BEFORE_HOOK)) {
                $arguments = $this->arguments(compact('original', 'extraArgs', 'number'));
                $original = $this->web->{$fn}($arguments);
            }

            $arguments = $this->arguments(
                ['target' => $original],
                compact('extraArgs', 'number')
            );

            return $this->tailor($this->methodTailor, self::BEFORE_HOOK, Abs::T_ARRAY, $arguments);
        };

        /**
         * after hook (row record)
         *
         * @param array $hooked
         * @param array $original
         * @param array $extraArgs
         * @param int   $index
         *
         * @return mixed
         */
        $after = function (array $hooked, array $original, array $extraArgs, int $index) use ($basicNumber) {

            $number = $basicNumber + $index;
            if (method_exists($this->web, $fn = $this->method . self::AFTER_HOOK)) {
                $arguments = $this->arguments(compact('hooked', 'original', 'extraArgs', 'number'));
                $hooked = $this->web->{$fn}($arguments);
            }

            $arguments = $this->arguments(
                ['target' => $hooked],
                compact('original', 'extraArgs', 'number')
            );

            return $this->tailor($this->methodTailor, self::AFTER_HOOK, Abs::T_ARRAY, $arguments);
        };

        $extraArgs = [Abs::HOOKER_FLAG_ACME => ['scene' => 'preview']];
        $_hooks = [];
        foreach ($hooks as $hook => $item) {
            $_hooks[$hook] = $item['fields'];
            $extraArgs[$hook] = array_merge($extraArgs[$hook] ?? [], $item['args']);
        }

        $original = $list;
        $list = $this->web->hooker($_hooks, $list, false, $before, $after, $extraArgs);
        $hooked = $list;

        /**
         * before render (all record)
         */

        $args = compact('hooked', 'original');

        $arguments = $this->arguments($args);
        $list = $this->caller($this->method, self::BEFORE_RENDER, Abs::T_ARRAY, $hooked, $arguments);

        $arguments = $this->arguments(['target' => $list], $args);
        $list = $this->tailor($this->methodTailor, self::BEFORE_RENDER, Abs::T_ARRAY, $arguments);

        /**
         * field charm
         */

        $charmList = [];
        $_list = $list ? current($list) : [];

        foreach ($_list as $field => $value) {
            $charm = self::CHARM . ucfirst($field);
            if (!method_exists($this->web, $this->method . $charm)) {
                continue;
            }
            $charmList[$field] = $charm;
        }

        $operate = Abs::TR_ACT;
        $maxButtons = 0;

        foreach ($list as $key => &$item) {

            /**
             * record operate - prepare
             */

            $arguments = $this->arguments(
                [
                    'item'      => $item,
                    'hooked'    => $hooked[$key],
                    'original'  => $original[$key],
                    'condition' => $this->input->condition,
                ]
            );
            $buttons = $this->caller($this->method, self::OPERATES, Abs::T_ARRAY, [], $arguments);

            $item[$operate] = null;
            $maxButtons = max($maxButtons, count($buttons));

            foreach ($buttons as $index => $button) {

                $buttonCls = Button::class;
                if (!Helper::extendClass($button, $buttonCls, true)) {
                    $fn = self::OPERATES;
                    throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
                }

                /**
                 * @var Button $button
                 */

                $button->setSize($this->input->recordOperatesSize);

                $button->setScript(Html::scriptBuilder($button->getClick(), $button->getArgs()));
                $button->setUrl($this->web->urlSafe($button->getRoute(), $button->getArgs(), 'Preview button'));

                $button->setDisabled(!$this->web->routeIsAccess($button->getRouteForAccess()));
                $item[$operate] .= $this->web->getButtonHtml($button);
            }

            $item[$operate] = "<div class='bsw-record-action'>{$item[$operate]}</div>";

            /**
             * field slot
             */

            foreach ($item as $field => &$value) {

                $charm = $charmList[$field] ?? false;
                if (!$charm) {
                    continue;
                }

                $fieldAnnotation = $previewAnnotation[$field] ?? [];
                $arguments = $this->arguments(
                    compact('value', 'item', 'fieldAnnotation'),
                    [
                        'hooked'        => $hooked[$key],
                        'original'      => $original[$key],
                        'valueHooked'   => $hooked[$key][$field],
                        'valueOriginal' => $original[$key][$field],
                    ]
                );
                $_value = $this->caller($this->method, $charm, null, null, $arguments);

                if (is_object($_value) && $_value instanceof Charm) {
                    $var = $_value->getVar();
                    $var = array_merge($var, ['value' => $_value->getValue()]);
                    $value = $this->parseSlot($_value->getCharm(), $field, $var);
                } elseif (is_scalar($_value)) {
                    $value = $_value;
                } else {
                    throw new ModuleException("{$this->method}{$charm}() should return scalar or " . Charm::class);
                }

                $output->slots[$field] = $this->parseSlot(Abs::SLOT_HTML_CONTAINER, $field);
            }
        }

        /**
         * slots & column for operate
         */

        if ($maxButtons > 0) {

            if (isset($output->columns[$operate]['width'])) {
                $width = $output->columns[$operate]['width'];
            } else {
                $maxButtons = min($maxButtons, 4);
                $width = 16 + ($maxButtons * (2 + 42 + 2) + ($maxButtons - 1) * 5) + 16;
                $output->scrollX += $width;
            }

            $output->columns[$operate] = array_merge(
                [
                    'title'       => $this->web->fieldLang('Action'),
                    'dataIndex'   => $operate,
                    'width'       => $width,
                    'align'       => Abs::POS_CENTER,
                    'scopedSlots' => ['customRender' => "__{$operate}"],
                ],
                $output->columns[$operate] ?? []
            );

            $output->slots[$operate] = $this->parseSlot(Abs::SLOT_HTML_CONTAINER, $operate);

        } else {
            unset($output->columns[$operate]);
        }

        if ($this->input->iframe) {
            $output->scrollX -= ($output->columns[$operate]['width'] ?? 0);
            unset($output->columns[$operate]);
        }

        return $list;
    }

    /**
     * Correct preview column of output
     *
     * @param array  $list
     * @param Output $output
     */
    protected function correctPreviewColumn(array $list, Output $output)
    {
        if (!($item = current($list))) {
            return;
        }

        foreach ($output->columns as $field => $preview) {
            if (array_key_exists($field, $item)) {
                continue;
            }
            $output->scrollX -= ($preview['width'] ?? 0);
            unset($output->columns[$field]);
        }
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

        try {
            $this->getQueryOptions();
        } catch (FilterException $e) {
            return $this->showError($e->getMessage(), ErrorParameter::CODE);
        }

        [$hooks, $previewAnnotation, $mixedAnnotation] = $this->handleAnnotation($output);

        $list = $this->getPreviewData($output, $mixedAnnotation);
        if ($list instanceof ArgsOutput) {
            return $list;
        }

        $list = $this->handlePreviewData($list, $hooks, $previewAnnotation, $output);
        $this->correctPreviewColumn($list, $output);

        $choice = $this->input->choice ?? new Choice();
        $arguments = $this->arguments(compact('choice'));
        $choice = $this->caller($this->method, self::CHOICE, Choice::class, $choice, $arguments);

        $arguments = $this->arguments(['target' => $choice]);
        $output->choice = $this->tailor($this->methodTailor, self::CHOICE, Choice::class, $arguments);

        $output->columns = array_values($output->columns);
        $output->columnsJson = Helper::jsonStringify($output->columns, '{}');

        $output->list = $list;
        $output->listJson = Helper::jsonStringify($output->list, '{}');
        $output->slotsJson = Helper::jsonStringify($output->slots, '{}');
        $output->pageJson = Helper::jsonStringify($output->page, '{}');

        $output->pageSizeOptions = array_map('strval', $output->pageSizeOptions);
        $output->pageSizeOptionsJson = Helper::jsonStringify($output->pageSizeOptions, '{}');

        $output->border = $this->input->border;
        $output->scroll = $this->input->scroll;
        $output->size = $this->input->size;
        $output->pageSizeOptions = $this->input->pageSizeOptions;
        $output->dynamic = $this->input->dynamic;
        $output->clsName = $this->input->clsName;

        $output = $this->caller(
            $this->method . Helper::underToCamel($this->name(), false),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}