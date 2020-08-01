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
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Radio;
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
    const FILTER_ANNOTATION      = 'FilterAnnotation';
    const FILTER_ANNOTATION_ONLY = 'FilterAnnotationOnly';
    const FILTER_OPERATE         = 'FilterOperates';
    const FILTER_CORRECT         = 'FilterCorrect';

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
        $query = $this->caller($this->method, self::QUERY, Abs::T_ARRAY, []);
        if ($this->entity && !isset($query['alias'])) {
            $query['alias'] = Helper::tableNameToAlias($this->entity);
        }

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

        $filterAnnotation = [];
        $filterAnnotationFull = [];
        $entityList = array_filter(array_unique($entityList));

        $extraArgs = [
            'enumClass'          => $this->input->enum,
            'doctrinePrefix'     => $this->web->parameter('doctrine_prefix'),
            'doctrinePrefixMode' => $this->web->parameter('doctrine_prefix_mode'),
        ];

        foreach ($entityList as $alias => $entity) {
            $filterAnnotationFull[$alias] = $this->web->getFilterAnnotation($entity, $extraArgs);
            foreach ($filterAnnotationFull[$alias] as $key => &$item) {
                $item['field'] = "{$alias}.{$item['field']}";
            }
        }

        if ($filterAnnotationFull) {
            $filterAnnotation = $filterAnnotationFull[$query['alias']];
        }

        return [$filterAnnotation, $filterAnnotationFull];
    }

    /**
     * Annotation handler
     *
     * @param array $query
     *
     * @return array
     * @throws
     */
    protected function handleAnnotation(array $query): array
    {
        /**
         * filter annotation
         */

        [$filterAnnotation, $filterAnnotationFull] = $this->listEntityFields($query);

        /**
         * preview annotation only
         */

        $fn = self::FILTER_ANNOTATION_ONLY;
        $filterAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, null);

        $arguments = $this->arguments(['target' => $filterAnnotationExtra], compact('filterAnnotation'));
        $filterAnnotationExtra = $this->tailor($this->method, $fn, [Abs::T_ARRAY, null], $arguments);

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

            $arguments->set('target', $filterAnnotationExtra);
            $filterAnnotationExtra = $this->tailor($this->method, $fn, Abs::T_ARRAY, $arguments);
        }

        /**
         * annotation handler with extra
         */

        foreach ($filterAnnotationExtra as $field => $item) {

            $itemHandling = $item;
            $defaultIndex = 0;
            [$field, $item] = $this->handleForAnnotationExtraItem($field, $item, $filterAnnotationFull, $defaultIndex);

            $fieldHandling = Helper::camelToUnder($field);
            if (strpos($fieldHandling, Abs::FILTER_INDEX_SPLIT) === false) {
                $field = $field . Abs::FILTER_INDEX_SPLIT . $defaultIndex;
            }

            if (!is_array($item)) {
                throw new ModuleException("Filter {$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if ($itemHandling === false) {
                $filterAnnotation[$field]['show'] = false;
            }

            if (isset($filterAnnotation[$field])) {
                $item = array_merge($filterAnnotation[$field], $item);
            }

            $original = $this->web->annotation(Filter::class, true);
            $original->class = $this->class;
            $original->target = $fieldHandling;

            $item = $original->converter([new Filter($item)]);
            $filterAnnotation[$field] = (array)current($item[Filter::class]);
        }

        $allowFields = [];
        foreach ($filterAnnotationFull as $alias => $item) {
            $allowFields = array_merge($allowFields, array_column($item, 'field'));
        }

        $annotationHandling = [];
        foreach ($filterAnnotation as $key => $item) {
            $key = Helper::camelToUnder($key);
            if (!$this->entity) {
                $annotationHandling[$key] = $item;
                continue;
            }

            $item['field'] = Helper::tableFieldAddAlias($item['field'], $query['alias']);
            if (in_array($item['field'], $allowFields)) {
                $annotationHandling[$key] = $item;
            }
        }

        $filterAnnotation = Helper::sortArray($annotationHandling, 'sort');

        /**
         * hooks
         */
        $hooks = [];
        foreach ($filterAnnotation as $field => $item) {
            $this->handleForFieldHook($field, $item['hook'], $hooks);
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
        $extraArgs = [Abs::HOOKER_FLAG_ACME => ['scene' => Abs::TAG_FILTER]];
        $hooksHandling = [];
        foreach ($hooks as $hook => $item) {
            $hooksHandling[$hook] = $item['fields'];
            $extraArgs[$hook] = array_merge($extraArgs[$hook] ?? [], $item['args']);
        }

        $filter = $this->web->getArgs($this->input->key) ?? [];
        $filter = Helper::numericValues($filter);

        $filterHandling = [];
        foreach ($filter as $key => $value) {
            if (strpos($key, Abs::FILTER_INDEX_SPLIT) === false) {
                $key = $key . Abs::FILTER_INDEX_SPLIT . 0;
            }
            $filterHandling[$key] = $value;
        }
        $filter = $this->web->hooker($hooksHandling, $filterHandling, true, null, null, $extraArgs);

        $condition = [];
        [$group, $diffuse] = $this->getFilterGroup($filterAnnotation);

        foreach ($filterAnnotation as $key => $item) {
            if (!isset($filter[$key]) && !isset($item['value'])) {
                continue;
            }

            if (isset($filter[$key])) {
                $filterAnnotation[$key]['value'] = $filter[$key];
            }

            $field = $item['field'];
            if ($item['group']) {
                if (!isset($condition[$field]) || is_scalar($condition[$field]['value'])) {
                    $condition[$field] = $this->web->createFilter(Senior::class, []);
                }

                $groupName = $diffuse[$key];
                $index = array_search($key, $group[$groupName]);
                $condition[$field]['value'][$index] = $filterAnnotation[$key]['value'];

            } else {
                $condition[$field] = $this->web->createFilter($item['filter'], $filterAnnotation[$key]['value']);
            }
        }

        foreach ($hooksHandling as $hook => $fields) {
            foreach ($fields as $index => $field) {
                if (!isset($filter[$field])) {
                    unset($hooksHandling[$hook][$index]);
                }
            }
            if (empty($hooksHandling[$hook])) {
                unset($hooksHandling[$hook]);
            }
        }

        $filterAnnotationValue = Helper::arrayColumn($filterAnnotation, 'value');
        $filterAnnotationValue = $this->web->hooker($hooksHandling, $filterAnnotationValue, false, null, null, $extraArgs);

        foreach ($filterAnnotation as $key => $item) {
            $filterAnnotation[$key]['value'] = $filterAnnotationValue[$key];
        }

        return [$filterAnnotation, $condition];
    }

    /**
     * Get item size
     *
     * @return string
     */
    protected function getSize(): string
    {
        return $this->input->mobile ? $this->input->filterFormSizeInMobile : $this->input->filterFormSize;
    }

    /**
     * Get item width
     *
     * @param int $column
     *
     * @return string
     */
    protected function getWidth(int $column): string
    {
        if ($this->input->mobile) {
            return '100%';
        }

        if (is_string($this->input->columnPx)) {
            return $this->input->columnPx;
        }

        $width = $this->input->columnPx * $column;

        return "{$width}px";
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

            $form->setKey($key);
            $form->setLabel($item['label']);
            $form->setStyle($item['style']);
            $form->setField($item['field']);

            if (isset($item['value'])) {
                $form->setValue($item['value']);
            }

            if (method_exists($form, 'setSize')) {
                $form->setSize($this->getSize());
            }

            /**
             * extra enum
             */

            $item = $this->handleForEnum($item, ['scene' => Abs::TAG_FILTER]);

            $enumClass = [
                Select::class,
                Radio::class,
                Checkbox::class,
            ];

            if (in_array(get_class($form), $enumClass)) {
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
                $form->setPlaceholder($item['placeholder'] ?: $form->getLabel());
            }

            $record[$key] = [
                'label'  => $item['trans'] ? $this->web->fieldLang($form->getLabel()) : $form->getLabel(),
                'column' => $item['column'],
                'width'  => $this->getWidth($item['column']),
                'type'   => $form,
                'sort'   => $item['sort'],
                'group'  => $item['group'],
                'title'  => $this->web->twigLang($item['title']),
            ];
        }

        $search = new Button('Search', $this->input->route, $this->input->cnf->icon_search);
        $search->setAttributes(['bsw-method' => Abs::TAG_SEARCH]);

        $export = null;
        if (
            $this->entity &&
            $this->input->scene === Abs::TAG_PREVIEW &&
            $this->input->showExport &&
            !$this->input->mobile &&
            !$this->input->iframe
        ) {
            $export = new Button('Export', $this->input->route, $this->input->cnf->icon_export, Abs::THEME_DEFAULT);
            $export->setAttributes(['bsw-method' => Abs::TAG_EXPORT]);
            $export->pushRouteForAccess($this->input->cnf->route_export);
            $export->pushRouteForAccess($this->input->route . Abs::FLAG_ROUTE_EXPORT);
        }

        $ops = compact('search', 'export');
        $operates = $this->caller($this->method, self::FILTER_OPERATE, Abs::T_ARRAY, [], $this->arguments($ops));
        $operates = array_filter(array_merge($ops, $operates));

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

            $operate->setHtmlType(Abs::TYPE_SUBMIT);
            $operate->setSize($this->getSize());
            if (!$this->web->routeIsAccess($operate->getRouteForAccess())) {
                $operate->setDisplay(false);
            }
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
        if ($this->input->mobile) {
            $output->maxShow = $this->input->maxShowInMobile;
        } elseif ($this->input->iframe) {
            $output->maxShow = $this->input->maxShowInIframe;
        } else {
            $output->maxShow = $this->input->maxShow;
        }

        [$output->group, $output->diffuse] = $this->getFilterGroup($filterAnnotation);
        $output->showFull = $this->getShowFilterItemList($filterAnnotation, $output->group, $output->diffuse);
        $output->showList = array_slice($output->showFull, 0, $output->maxShow);

        $output->showFullJson = Helper::jsonStringify($output->showFull);
        $output->showListJson = Helper::jsonStringify($output->showList);
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
                        'width'  => $this->getWidth($output->filter[$field]['column']),
                        'type'   => [],
                        'sort'   => $output->filter[$field]['sort'],
                        'group'  => $name,
                        'title'  => $output->filter[$field]['title'],
                    ];
                }
                /**
                 * @var Form $type
                 */
                $type = $output->filter[$field]['type'];
                $output->filter[$name]['type'][] = $type;
                foreach (['label', 'column', 'sort', 'title'] as $key) {
                    if (empty($output->filter[$name][$key])) {
                        $output->filter[$name][$key] = $output->filter[$field][$key];
                    }
                }
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

        $query = $this->getQueryOptions();
        [$filterAnnotation, $hooks] = $this->handleAnnotation($query);

        [$filter, $condition] = $this->getFilterData($filterAnnotation, $hooks);
        [$filter, $condition] = $this->caller(
            $this->method,
            self::FILTER_CORRECT,
            Abs::T_ARRAY,
            [$filter, $condition],
            $this->arguments(compact('filter', 'condition'))
        );

        [$output->filter, $output->operates, $format] = $this->handleFilterData($filter);
        $output->showLabel = $this->input->showLabel;
        $output->condition = $condition;
        $output->formatJson = Helper::jsonFlexible($format);

        $this->handleShowList($filterAnnotation, $output);
        $this->handleFilter($output);

        $output->columnPx = $this->input->columnPx;
        $output->textShow = $this->input->textShow;
        $output->textHide = $this->input->textHide;
        $output->size = $this->getSize();

        $output = $this->caller(
            $this->method(),
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}