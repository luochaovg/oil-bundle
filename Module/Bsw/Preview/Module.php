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
use Leon\BswBundle\Module\Filter\Dispatcher as FilterDispatcher;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\ModuleException;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const QUERY_PARENT   = 'QueryParent';
    const QUERY_CHILDREN = 'QueryChildren';
    const BEFORE_HOOK    = 'BeforeHook';
    const AFTER_HOOK     = 'AfterHook';
    const CHOICE         = 'Choice';
    const BEFORE_RENDER  = 'BeforeRender';
    const CHARM          = 'Charm';
    const OPERATES       = 'RecordOperates';
    const MIXED_HANDLER  = 'MixedHandler';
    const PREVIEW_DATA   = 'PreviewData';

    /**
     * @const string
     */
    const DRESS_DEFAULT = 'default';

    /**
     * @var string
     */
    protected $methodTailor = 'tailorPreview';

    /**
     * @var bool
     */
    protected $isExport = false;

    /**
     * @var array
     */
    protected $long2sort = [
        Abs::SORT_ASC_LONG  => Abs::SORT_ASC,
        Abs::SORT_DESC_LONG => Abs::SORT_DESC,
    ];

    /**
     * @return string
     */
    public function name(): string
    {
        return 'preview';
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
     * @return bool
     */
    protected function isChildrenNecessary(): bool
    {
        return $this->input->childrenRelationField && !$this->isExport && !$this->input->condition;
    }

    /**
     * Get query name
     *
     * @param int $parentId
     *
     * @return string
     */
    protected function choiceQueryMethod(?int $parentId = null): string
    {
        if ($this->isChildrenNecessary()) {
            return $parentId ? self::QUERY_CHILDREN : self::QUERY_PARENT;
        }

        return self::QUERY;
    }

    /**
     * Get query options
     *
     * @param int $parentId
     *
     * @return array
     * @throws
     */
    protected function getQueryOptions(?int $parentId = null): array
    {
        $fn = $this->choiceQueryMethod($parentId);
        $arguments = $parentId ? $this->arguments(['parent' => $parentId], $this->input->args) : null;
        $query = $this->caller($this->method, $fn, Abs::T_ARRAY, [], $arguments);

        if ($fn === self::QUERY_CHILDREN && empty($query)) {
            throw new ModuleException("Query {$this->class}::{$this->method}{$fn}():array returned must be not empty");
        }

        if ($this->entity && !isset($query['alias'])) {
            $query['alias'] = Helper::tableNameToAlias($this->entity);
        }

        $condition = (new FilterDispatcher())->filterList($this->input->condition, FilterDispatcher::DQL_MODE);
        $query = Helper::merge($query, $condition);

        return $query;
    }

    /**
     * List entity fields
     *
     * @param array $query
     *
     * @return array
     * @throws
     */
    protected function listEntityFields(array $query): array
    {
        $entityList = $this->listEntityBasicFields($query);

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
     * Render handler
     *
     * @param string $render
     *
     * @return string
     */
    protected function renderHandler(string $render): string
    {
        if (Helper::strEndWith($render, Abs::HTML_SUFFIX)) {
            $render = $this->web->caching(
                function () use ($render) {
                    return $this->web->renderPart($render);
                }
            );
        }

        return $render;
    }

    /**
     * Create slot template
     *
     * @param string $field
     * @param array  $item
     *
     * @return string
     * @throws
     */
    protected function createSlot(string $field, array $item): string
    {
        /**
         * extra enum
         */

        $item = $this->handleForEnumExtra($item, ['scene' => Abs::TAG_PREVIEW]);

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
                $dressStringify = Helper::jsonFlexible($item['dress']);
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
            return $this->parseSlot($this->renderHandler($render), $field, [], Abs::SLOT_CONTAINER);
        }

        return $this->parseSlot('{:value}', $field);
    }

    /**
     * Annotation handler
     *
     * @param array  $query
     * @param Output $output
     *
     * @return array
     * @throws
     */
    protected function handleAnnotation(array $query, Output $output): array
    {
        /**
         * preview annotation
         */

        [$previewAnnotation, $previewAnnotationFull, $mixedAnnotation] = $this->listEntityFields($query);

        /**
         * preview annotation only
         */

        $fn = self::ANNOTATION_ONLY;
        $operate = Abs::TR_ACT;

        $previewAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, null);

        $arguments = $this->arguments(
            ['target' => $previewAnnotationExtra],
            compact('previewAnnotation'),
            $this->input->args
        );
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

            $previewAnnotationExtra = $this->caller(
                $this->method,
                $fn,
                Abs::T_ARRAY,
                [],
                $this->arguments($this->input->args)
            );
            if (!isset($previewAnnotationExtra[$operate])) {
                $previewAnnotationExtra[$operate] = ['show' => true];
            }

            $arguments->set('target', $previewAnnotationExtra);
            $previewAnnotationExtra = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $arguments);
        }

        /**
         * annotation handler with extra
         */

        foreach ($previewAnnotationExtra as $field => $item) {

            $itemHandling = $item;
            [$field, $item] = $this->handleForAnnotationExtraItem($field, $item, $previewAnnotationFull);

            if (!is_array($item)) {
                throw new ModuleException("Preview {$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if ($itemHandling === false) {
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
        $arguments = $this->arguments(['mixed' => $mixedAnnotation], $this->input->args);
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
        $hooks = $columns = $slots = $customRenders = [];

        foreach ($previewAnnotation as $field => $item) {
            $this->handleForFieldHook($field, $item['hook'], $hooks);
            if (!$item['show']) {
                continue;
            }

            $column = [
                'title'     => $this->web->fieldLang($item['label']),
                'dataIndex' => $field,
                'fixed'     => $item['fixed'],
                'align'     => $item['align'],
                'class'     => $item['clsName'],
                'ellipsis'  => $item['ellipsis'],
                'colSpan'   => $item['headerColumn'],
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

            if ($customRender = $item['customRender']) {
                $customRenders[$field] = "fn:{$customRender}";
            }

            /**
             * slot handler
             */

            $column['scopedSlots'] = ['customRender' => "__{$field}"];
            $slots[$field] = $this->createSlot($field, $item);

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
        $output->customRenders = $customRenders;

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
        $arguments = $this->arguments(['condition' => $this->input->condition, 'query' => $query], $this->input->args);
        $previewData = $this->caller($this->method, self::PREVIEW_DATA, Abs::T_ARRAY, null, $arguments);

        if (!is_array($previewData)) {
            $fn = self::PREVIEW_DATA;
            throw new ModuleException("{$this->class}::{$this->method}{$fn}() must configure and return be array");
        }

        return $this->web->manualListForPagination($previewData, $query);
    }

    /**
     * Get preview data
     *
     * @param array  $query
     * @param Output $output
     * @param array  $mixedAnnotation
     * @param int    $parentId
     *
     * @return array|ArgsOutput
     * @throws ModuleException
     */
    protected function getPreviewData(array $query, Output $output, array $mixedAnnotation, int $parentId = 0)
    {
        /**
         * sequence for order
         */

        if ($mixedAnnotation) {

            $sequence = $this->web->getArgs(Abs::TAG_SEQUENCE);
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

                $query[$made] = [$mixed['field'] => $this->long2sort[$direction]];
                unset($query[$unmade]);
                break;
            }

            $sequence = array_merge(
                $query[Abs::ORDER] ?? [],
                $query[Abs::SORT] ?? []
            );

            if ($sequence) {
                $key = Helper::tableFieldDelAlias(key($sequence));
                $output->columns[$key]['defaultSortOrder'] = array_flip($this->long2sort)[current($sequence)];
            }
        }

        /**
         * list by query
         */

        $query = array_merge(
            [
                'paging' => true,
                'page'   => 1,
                'limit'  => Abs::PAGE_DEFAULT_SIZE,
            ],
            $query
        );

        if (!$parentId) {
            if (($page = intval($this->web->getArgs(Abs::PG_PAGE))) > 0) {
                $query['page'] = intval($page) ?: 1;
            }

            if (($limit = intval($this->web->getArgs(Abs::PG_PAGE_SIZE))) > 0) {
                if (in_array($limit, Abs::PG_PAGE_SIZE_OPTIONS)) {
                    $query['limit'] = $limit;
                }
            }
        }

        $fn = $this->choiceQueryMethod($parentId);

        /**
         * Fetch data
         */

        if ($this->entity) {

            if (!isset($query[Abs::ORDER])) {
                $pk = $this->repository->pk();
                $query[Abs::ORDER] = ["{$query['alias']}.{$pk}" => Abs::SORT_DESC];
            }

            $arguments = $this->arguments(['target' => $query], $this->input->args);
            $query = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $arguments);

            if ($this->isExport) {
                $sets = Helper::createSignature(
                    [
                        'entity' => base64_encode($this->entity),
                        'query'  => Helper::objectToString($query),
                    ],
                    $this->web->parameter('salt')
                );

                return $this->showMessage((new Message())->setSets($sets));
            }

            $list = $this->repository->lister($query);

        } else {
            $arguments = $this->arguments(['target' => $query], $this->input->args);
            $query = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $arguments);
            $list = $this->manualLister($query);
        }

        if (!$parentId) {
            $output->query = $query;
        }

        /**
         * pagination
         */

        if ($query['paging']) {

            $page = $list;
            $list = Helper::dig($page, Abs::PG_ITEMS);
            if (!$parentId) {
                $output->page = [
                    'currentPage' => $page[Abs::PG_CURRENT_PAGE],
                    'pageSize'    => $page[Abs::PG_PAGE_SIZE],
                    'totalPage'   => $page[Abs::PG_TOTAL_PAGE],
                    'totalItem'   => $page[Abs::PG_TOTAL_ITEM],
                ];
            }
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
     * @param int    $parentId
     *
     * @return array
     * @throws
     */
    protected function handlePreviewData(
        array $list,
        array $hooks,
        array $previewAnnotation,
        Output $output,
        int $parentId = 0
    ): array {

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
        $before = function (array $original, array $extraArgs, int $index) use ($basicNumber, $parentId) {
            $number = $basicNumber + $index;
            if ($parentId) {
                $original[Abs::TAG_ROW_CLS_NAME] = $this->input->childrenRowClsName;
            }
            $arguments = $this->arguments(compact('original', 'extraArgs', 'number'), $this->input->args);
            $original = $this->caller($this->method, self::BEFORE_HOOK, Abs::T_ARRAY, $original, $arguments);

            return $this->tailor(
                $this->methodTailor,
                self::BEFORE_HOOK,
                Abs::T_ARRAY,
                $arguments->unset('original')->set('target', $original)
            );
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
            $arguments = $this->arguments(compact('hooked', 'original', 'extraArgs', 'number'), $this->input->args);
            $hooked = $this->caller($this->method, self::AFTER_HOOK, Abs::T_ARRAY, $hooked, $arguments);

            return $this->tailor(
                $this->methodTailor,
                self::AFTER_HOOK,
                Abs::T_ARRAY,
                $arguments->unset('hooked')->set('target', $hooked)
            );
        };

        $original = $list;
        $extraArgs = [Abs::HOOKER_FLAG_ACME => ['scene' => Abs::TAG_PREVIEW]];
        $list = $this->web->hooker($hooks, $list, false, $before, $after, $extraArgs);
        $hooked = $list;

        /**
         * before render (all record)
         */

        $args = compact('hooked', 'original');

        $arguments = $this->arguments($args, $this->input->args);
        $list = $this->caller($this->method, self::BEFORE_RENDER, Abs::T_ARRAY, $hooked, $arguments);

        $arguments->set('target', $list);
        $list = $this->tailor($this->methodTailor, self::BEFORE_RENDER, Abs::T_ARRAY, $arguments);

        /**
         * field charm
         */

        $charmList = [];
        $listHandling = $list ? current($list) : [];

        foreach ($listHandling as $field => $value) {
            $charm = self::CHARM . ucfirst($field);
            if (!method_exists($this->web, $this->method . $charm)) {
                continue;
            }
            $charmList[$field] = $charm;
        }

        $operate = Abs::TR_ACT;
        $maxButtons = 0;
        $maxButtonsDisplay = [];

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
                ],
                $this->input->args
            );

            $recordOperates = [];
            $buttons = $this->caller($this->method, self::OPERATES, Abs::T_ARRAY, [], $arguments);
            $coverArgs = $this->web->parameters('cover_iframe_args_by_name') ?? [];

            foreach ($buttons as $index => $button) {
                $buttonCls = Button::class;
                if (!Helper::extendClass($button, $buttonCls, true)) {
                    $fn = self::OPERATES;
                    throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
                }

                /**
                 * @var Button $button
                 */
                if ($name = $button->getName()) {
                    $buttonArgs = array_merge($button->getArgs(), $coverArgs[$name] ?? []);
                    $button->setArgs($buttonArgs);
                }

                if ($this->input->actionBtnForceLink) {
                    $button->setType(Abs::THEME_LINK)->appendStyle(['padding' => '0', 'margin' => '3px 0']);
                }

                if ($this->input->actionBtnForceNoIcon) {
                    $button->setIcon(null);
                }

                $button->setSize($this->input->recordOperatesSize);

                $button->setScript(Html::scriptBuilder($button->getClick(), $button->getArgs()));
                $button->setUrl($this->web->urlSafe($button->getRoute(), $button->getArgs(), 'Build record operates'));

                // instead of display with remove
                if (!$this->web->routeIsAccess($button->getRouteForAccess())) {
                    unset($buttons[$index]);
                    continue;
                }

                array_push($recordOperates, $this->web->getButtonHtml($button));
            }

            $buttonsDisplay = [];
            foreach ($buttons as $button) {
                if ($button->isDisplay()) {
                    array_push(
                        $buttonsDisplay,
                        [
                            'label' => $this->web->twigLang($button->getLabel()),
                            'icon'  => $button->getIcon(),
                        ]
                    );
                }
            }

            if (($count = count($buttonsDisplay)) > $maxButtons) {
                $maxButtons = $count;
                $maxButtonsDisplay = $buttonsDisplay;
            }

            $recordOperates = implode($this->input->actionBtnSplit, $recordOperates);
            $item[$operate] = Html::tag('div', $recordOperates, ['class' => 'bsw-record-action']);

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
                        'valueOriginal' => $original[$key][$field] ?? null, // when addition by yourself it's be null
                    ],
                    $this->input->args
                );

                $crm = $this->caller($this->method, $charm, null, null, $arguments);

                if (is_object($crm) && $crm instanceof Charm) {
                    $var = $crm->getVar();
                    $var = array_merge($var, ['value' => $crm->getValue()]);
                    $render = $this->renderHandler($crm->getCharm());
                    $value = $this->parseSlot($render, $field, $var);
                } elseif (is_scalar($crm)) {
                    $value = $crm;
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
                $buttonWidth = 0;
                foreach ($maxButtonsDisplay as $i) {
                    $buttonWidth += Helper::textWidthPxByMap($i['label'], $this->input->actionByteMapPx);
                    if ($i['icon']) {
                        $buttonWidth += $this->input->actionBtnIconWidth;
                    }
                }
                $width = $buttonWidth;
                $width += $this->input->actionColBorder * 2;
                $width += ($maxButtons * $this->input->actionBtnBorder * 2);
                $width += ($maxButtons - 1) * $this->input->actionBtnGap;
                $width = max($width, 64);
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

        if ($this->getInputAuto('removeOperate')) {
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
     * Get children relation field
     *
     * @return string|null
     */
    protected function getChildrenRelationField(): ?string
    {
        if (!$this->input->childrenRelationField) {
            return null;
        }

        if (is_string($this->input->childrenRelationField)) {
            return $this->input->childrenRelationField;
        }

        return $this->entity ? $this->repository->pk() : null;
    }

    /**
     * Get preview data with children
     *
     * @param array  $list
     * @param string $parentKey
     * @param array  $hooks
     * @param Output $output
     * @param array  $previewAnnotation
     * @param array  $mixedAnnotation
     *
     * @return array
     * @throws
     */
    protected function getPreviewDataWithChildren(
        array $list,
        string $parentKey,
        array $hooks,
        Output $output,
        array $previewAnnotation,
        array $mixedAnnotation
    ): array {

        foreach ($list as &$record) {
            $parentId = $record[$parentKey] ?? null;
            if (empty($parentId)) {
                break;
            }
            $query = $this->getQueryOptions($parentId);
            $childrenList = $this->getPreviewData($query, $output, $mixedAnnotation, $parentId);
            if ($childrenList) {
                $childrenList = $this->handlePreviewData(
                    $childrenList,
                    $hooks,
                    $previewAnnotation,
                    $output,
                    $parentId
                );
                $this->correctPreviewColumn($childrenList, $output);
                $record[Abs::TAG_CHILDREN] = $this->getPreviewDataWithChildren(
                    $childrenList,
                    $parentKey,
                    $hooks,
                    $output,
                    $previewAnnotation,
                    $mixedAnnotation
                );
            }
        }

        return $list;
    }

    /**
     * @return ArgsOutput
     * @throws
     */
    public function logic(): ArgsOutput
    {
        $output = new Output($this->input);
        $this->isExport = $this->input->ajax && ($this->web->getArgs(Abs::TAG_SCENE) === Abs::TAG_EXPORT);

        /**
         * handle annotation
         */

        try {
            $query = $this->getQueryOptions();
        } catch (FilterException $e) {
            return $this->showError($e->getMessage(), ErrorParameter::CODE);
        }

        [$hooks, $previewAnnotation, $mixedAnnotation] = $this->handleAnnotation($query, $output);

        $list = $this->getPreviewData($query, $output, $mixedAnnotation);
        if ($list instanceof ArgsOutput) {
            return $list;
        }

        $list = $this->handlePreviewData($list, $hooks, $previewAnnotation, $output);
        $this->correctPreviewColumn($list, $output);

        /**
         * children
         */

        if ($this->entity && $this->isChildrenNecessary()) {
            $list = $this->getPreviewDataWithChildren(
                $list,
                $this->getChildrenRelationField(),
                $hooks,
                $output,
                $previewAnnotation,
                $mixedAnnotation
            );
        }

        $choice = $this->input->choice ?? new Choice();
        $arguments = $this->arguments(compact('choice'), $this->input->args);
        $choice = $this->caller($this->method, self::CHOICE, Choice::class, $choice, $arguments);
        $arguments = $this->arguments(['target' => $choice], $this->input->args);
        $output->choice = $this->tailor($this->methodTailor, self::CHOICE, Choice::class, $arguments);

        $output->columns = array_values($output->columns);
        $output->columnsJson = Helper::jsonStringify($output->columns);
        $output->customRendersJson = Helper::jsonFlexible($output->customRenders);

        $output->list = $list;
        $output->listJson = Helper::jsonStringify($output->list);
        $output->pageJson = Helper::jsonFlexible($output->page);

        $output->choiceFixed = $this->getInputAuto('choiceFixed');
        $output->border = $this->getInputAuto('border');
        $output->size = $this->getInputAuto('size');
        $output->pageSizeOptions = array_map('strval', $this->input->pageSizeOptions);
        $output->pageSizeOptionsJson = Helper::jsonStringify($output->pageSizeOptions);
        $output->scrollXOperate = $this->getInputAuto('scrollXOperate');

        if ($this->input->childrenRelationField) {
            $output->loadTwice = true;
        }

        $output = $this->caller(
            $this->method(),
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'), $this->input->args)
        );

        return $output;
    }
}