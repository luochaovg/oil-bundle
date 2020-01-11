<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Annotation\Entity\Preview;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\ModuleException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @property Input                $input
 * @property BswBackendController $web
 *
 * @license variables
 *
 * 1.模板自动变量
 *
 * {field}          = 字段唯一标记
 * {:value}         = 字符串 "value"
 * {value}          = 字符串 "{{ value }}"
 * {Abs::TPL_XXX}   = "Abs:TPL_XXX" 常量对应的值 ("TPL_"开头的常量)
 * {Abs::SLOT_XXX}  = "Abs:SLOT_XXX" 常量对应的值 ("SLOT_"开头的常量)
 *
 * 2.可能被修改的变量
 *
 * tpl                  = 子模板
 * dress                = dress 对应的字符串/JSON字符串
 * enum                 = enum 对应的JSON字符串
 * value                = enum 对应的JSON字符串
 * Abs::SLOT_NOT_BLANK  = enum 对应的JSON字符串
 *
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const BEFORE_HOOK   = 'BeforeHook';     // [单行数据] 钩子前处理
    const AFTER_HOOK    = 'AfterHook';      // [单行数据] 钩子后处理
    const QUERY         = 'Query';          // [全局配置] 列表查询
    const CHOICE        = 'Choice';         // [全局配置] 列表选择
    const BEFORE_RENDER = 'BeforeRender';   // [全量数据] 渲染前处理
    const CHARM         = 'Charm';          // [字段的值] 个性化装饰
    const OPERATES      = 'RecordOperates'; // [单行数据] 操作按钮组

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

        $query = $this->web->filter($this->input->condition);
        $this->query = Helper::merge2(true, false, true, $this->query, $query);

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
            $annotationFull[$alias] = $this->web->getPreviewAnnotation($entity, $this->input->enum);
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
        $_field = Helper::dig($item, 'field');

        $clone = $annotationFull[$_table][$_field] ?? [];
        $item = empty($clone) ? [] : array_merge($clone, $item);

        return [$_field, $item];
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

        $item = $this->handleForEnum($item);

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
         * text use dress (dress type be string)
         */

        if ($item['dress'] && !$item['enum']) {

            if (!is_string($item['dress'])) {
                $exception = $this->getAnnotationException($field);
                throw new AnnotationException(
                    "{$exception} option `dress` should be string when not enum"
                );
            }

            return $this->parseSlot($item['dress'], $field, [], Abs::SLOT_CONTAINER);
        }

        /**
         * choice list (enum) use dress (dress type be sting or array)
         */

        if ($item['dress'] && $item['enum']) {

            $dressArray = false;
            if (is_array($item['dress'])) {
                $dressStringify = $this->web->enumEncode($item['dress']);
                $item['dress'] = "{$dressStringify}[value]";
                $dressArray = true;
            }

            $enumStringify = $this->web->enumLang($item['enum'], true);

            $var = [
                'Abs::SLOT_NOT_BLANK' => "{$enumStringify}[value]",
                'enum'                => "{$enumStringify}[value]",
                'dress'               => "{$item['dress']}",
            ];

            if ($item['status']) {
                $tpl = Abs::TPL_ENUM_STATE;
            } else {
                $tpl = $dressArray ? Abs::TPL_ENUM_1_DRESS : Abs::TPL_ENUM_2_DRESS;
                $var['value'] = "{{ {$enumStringify}[value] }}";
            }

            return $this->parseSlot($tpl, $field, $var, Abs::SLOT_CONTAINER);
        }

        /**
         * choice list (enum) without dress
         */

        if (!$item['dress'] && $item['enum']) {

            $enumStringify = $this->web->enumLang($item['enum'], true);
            $var = [
                'Abs::SLOT_NOT_BLANK' => "{$enumStringify}[value]",
                'value'               => "{{ {$enumStringify}[value] }}",
            ];

            return $this->parseSlot(Abs::TPL_ENUM_0_DRESS, $field, $var, Abs::SLOT_CONTAINER);
        }

        /**
         * test use render
         */

        if ($item['render']) {
            return $this->parseSlot($item['render'], $field, [], Abs::SLOT_CONTAINER);
        }

        return false;
    }

    /**
     * Annotation handler
     *
     * @param Output $output
     * @param array  $mixedAnnotation
     *
     * @return array
     * @throws
     */
    protected function handleAnnotation(Output $output, array $mixedAnnotation): array
    {
        /**
         * preview annotation
         */

        [$annotation, $annotationFull] = $this->listEntityFields();

        /**
         * preview extra annotation
         */

        $fn = self::ANNOTATION;
        $operate = Abs::TR_ACT;

        $annotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, []);
        if (!isset($annotationExtra[$operate])) {
            $annotationExtra[$operate] = ['show' => true];
        }

        $annotationExtra = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $annotationExtra, $annotation);

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

            $original = $this->web->annotation(Preview::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Preview($item)]);
            $annotation[$field] = (array)current($item[Preview::class]);
        }

        $annotation = Helper::sortArray($annotation, 'sort');

        /**
         * hooks & columns
         */

        $scroll = 0;
        $hooks = $columns = $dress = [];

        foreach ($annotation as $field => $item) {

            foreach ($item['hook'] as $hook) {
                $hooks[$hook][] = $field;
            }

            if (!$item['show']) {
                continue;
            }

            $column = [
                'title'     => $this->web->labelLang($item['label']),
                'dataIndex' => $field,
                'fixed'     => $item['fixed'],
            ];

            if ($field === Abs::PK && is_null($column['fixed'])) {
                $column['fixed'] = 'left';
            }

            if ($field === $operate) {
                if (is_null($column['fixed'])) {
                    $column['fixed'] = 'right';
                }
                if (!isset($annotationExtra[$operate]['width'])) {
                    unset($item['width']);
                }
            }

            if ($width = ($item['width'] ?? false)) {
                $column['width'] = $width;
                $scroll += $width;
            }

            if ($align = $item['align']) {
                $column['align'] = $align;
            }

            /**
             * dress handler
             */

            $slot = $this->createSlot($field, $item, $field);

            if ($slot !== false) {
                $column['scopedSlots'] = ['customRender' => $field];
                $dress[$field] = $slot;
            }

            /**
             * sorter
             */

            if ($mixed = $mixedAnnotation[$field] ?? null) {
                foreach (['order', 'sort'] as $keyword) {

                    if (!$mixed[$keyword]) {
                        continue;
                    }

                    $column['sorter'] = true;
                    $column['sortDirections'] = $mixed["{$keyword}Directions"];
                }
            }

            $columns[$field] = $column;
        }

        $output->scroll = $scroll;
        $output->dress = $dress;
        $output->columns = $columns;

        return $hooks;
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
     * @return array
     * @throws ModuleException
     */
    protected function getPreviewData(Output $output, array $mixedAnnotation): array
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

                $made = $mixedAnnotation[$key][Abs::ORDER] ? Abs::ORDER : Abs::SORT;
                $unmade = $mixedAnnotation[$key][Abs::ORDER] ? Abs::SORT : Abs::ORDER;

                $this->query[$made] = ["{$this->query['alias']}.{$key}" => $this->long2sort[$direction]];
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

        $query = array_merge(
            [
                'paging' => true,
                'page'   => intval($this->web->getArgs('page') ?? 1),
            ],
            $this->query
        );

        /**
         * Fetch data
         */

        if ($this->entity) {

            if (!isset($query['order'])) {
                $pk = $this->repository->pk();
                $query['order'] = ["{$query['alias']}.{$pk}" => Abs::SORT_DESC];
            }

            $query = $this->tailor($this->methodTailor, self::QUERY, Abs::T_ARRAY, $query);
            $list = $this->repository->lister($query);

        } else {
            $query = $this->tailor($this->methodTailor, self::QUERY, Abs::T_ARRAY, $query);
            $list = $this->manualLister($query);
        }

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
     * @param Output $output
     *
     * @return array
     * @throws
     */
    protected function handlePreviewData(array $list, array $hooks, Output $output): array
    {
        /**
         * before hook (row record)
         *
         * @param array $origin
         * @param array $extraArgs
         *
         * @return mixed
         */
        $before = function (array $origin, array $extraArgs) {
            if (method_exists($this->web, $fn = $this->method . self::BEFORE_HOOK)) {
                $origin = $this->web->{$fn}($origin, $extraArgs);
            }

            return $this->tailor($this->methodTailor, self::BEFORE_HOOK, Abs::T_ARRAY, $origin, $extraArgs);
        };

        /**
         * after hook (row record)
         *
         * @param array $hooked
         * @param array $origin
         * @param array $extraArgs
         *
         * @return mixed
         */
        $after = function (array $hooked, array $origin, array $extraArgs) {
            if (method_exists($this->web, $fn = $this->method . self::AFTER_HOOK)) {
                $hooked = $this->web->{$fn}($hooked, $origin, $extraArgs);
            }

            return $this->tailor($this->methodTailor, self::AFTER_HOOK, Abs::T_ARRAY, $hooked, $origin, $extraArgs);
        };

        $original = $list;
        $list = $this->web->hooker($hooks, $list, false, $before, $after);
        $hooked = $list;

        /**
         * before render (all record)
         */

        $args = [$hooked, $original];
        $list = $this->caller($this->method, self::BEFORE_RENDER, Abs::T_ARRAY, $hooked, $args);
        $list = $this->tailor($this->methodTailor, self::BEFORE_RENDER, Abs::T_ARRAY, $list, ...$args);

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

            $args = [$item, $hooked[$key], $original[$key]];
            $buttons = $this->caller($this->method, self::OPERATES, Abs::T_ARRAY, [], $args);

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

                $button->setSize(Button::SIZE_SMALL);

                $button->setScript(Html::scriptBuilder($button->getClick(), $button->getArgs()));
                try {
                    $button->setUrl($this->web->url($button->getRoute(), $button->getArgs(), false));
                } catch (RouteNotFoundException $e) {
                    $this->input->logger->warning("Preview button route error, {$e->getMessage()}");
                }

                $button->setDisabled(!$this->web->routeIsAccess($button->getRouteForAccess()));
                $item[$operate] .= $this->web->renderPart('@LeonBsw/form/button.native', ['form' => $button]);
            }

            $item[$operate] = "<div class='bsw-record-action'>{$item[$operate]}</div>";

            /**
             * field dress
             */

            foreach ($item as $field => &$value) {

                $charm = $charmList[$field] ?? false;
                if (!$charm) {
                    continue;
                }

                $args = [$value, $hooked[$key][$field], $original[$key][$field], $item, $hooked, $original];
                $_value = $this->caller($this->method, $charm, null, null, $args);

                if (is_object($_value) && $_value instanceof Charm) {
                    $value = $this->parseSlot($_value->getCharm(), '', ['value' => $_value->getValue($value)]);
                } elseif (is_scalar($_value)) {
                    $value = $_value;
                } else {
                    throw new ModuleException("{$this->method}{$charm}() should return scalar or " . Charm::class);
                }

                $output->dress[$field] = $this->parseSlot(Abs::SLOT_HTML_CONTAINER, $field);
            }
        }

        /**
         * dress & column for operate
         */

        if ($maxButtons > 0) {

            if (isset($output->columns[$operate]['width'])) {
                $width = $output->columns[$operate]['width'];
            } else {
                $maxButtons = min($maxButtons, 4);
                $width = 16 + ($maxButtons * (2 + 40 + 2) + ($maxButtons - 1) * 5) + 16;
                $output->scroll += $width;
            }

            $output->columns[$operate] = array_merge(
                [
                    'title'       => $this->web->labelLang('Action'),
                    'dataIndex'   => $operate,
                    'width'       => $width,
                    'align'       => Abs::POS_CENTER,
                    'scopedSlots' => ['customRender' => $operate],
                ],
                $output->columns[$operate] ?? []
            );

            $output->dress[$operate] = $this->parseSlot(Abs::SLOT_HTML_CONTAINER, $operate);

        } else {
            unset($output->columns[$operate]);
        }

        if ($this->input->iframe) {
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
            if (isset($item[$field])) {
                continue;
            }
            $output->scroll -= ($preview['width'] ?? 0);
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

        $this->getQueryOptions();
        $mixedAnnotation = [];
        if ($this->entity) {
            $mixedAnnotation = $this->web->getMixedAnnotation($this->entity, $this->input->enum);
        }

        $hooks = $this->handleAnnotation($output, $mixedAnnotation);

        $list = $this->getPreviewData($output, $mixedAnnotation);
        $list = $this->handlePreviewData($list, $hooks, $output);
        $this->correctPreviewColumn($list, $output);

        /**
         * assign variable to output
         *
         * @var Choice $choice
         */

        $choice = $this->input->choice ?? new Choice();
        $choice = $this->caller($this->method, self::CHOICE, Choice::class, $choice, [$choice]);
        $output->choice = $this->tailor($this->methodTailor, self::CHOICE, Choice::class, $choice);

        $output->columns = array_values($output->columns);
        $output->columnsJson = $this->json($output->columns);

        $output->list = $list;
        $output->listJson = $this->json($output->list);
        $output->dressJson = $this->json($output->dress);
        $output->pageJson = $this->json($output->page);

        $output->dynamic = $this->input->dynamic;
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