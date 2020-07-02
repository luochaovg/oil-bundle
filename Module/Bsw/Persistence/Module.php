<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Annotation\Entity\Persistence;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Leon\BswBundle\Module\Exception\LogicException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Module\Form\Entity\Group;
use Symfony\Component\Validator\Exception\ValidatorException;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Form\Entity\CkEditor;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Radio;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Module\Form\Entity\Upload;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Hook\Entity\JsonStringify;
use Leon\BswBundle\Component\Upload as Uploader;
use Exception;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const BEFORE_HOOK        = 'BeforeHook';
    const AFTER_HOOK         = 'AfterHook';
    const BEFORE_RENDER      = 'BeforeRender';
    const FORM_OPERATE       = 'FormOperates';
    const AFTER_SUBMIT       = 'AfterSubmit';
    const BEFORE_PERSISTENCE = 'BeforePersistence';
    const AFTER_PERSISTENCE  = 'AfterPersistence';

    /**
     * @var string
     */
    protected $methodTailor = 'tailorPersistence';

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
        return 'persistence';
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
     * @param string $field
     *
     * @return string
     */
    protected function getAnnotationException(string $field): string
    {
        if ($this->entity) {
            return "@Persistence() in {$this->entity}::{$field}";
        }

        $annotation = self::ANNOTATION;

        return "Item in {$this->method}{$annotation}():array {$field}";
    }

    /**
     * Annotation handler
     *
     * @param array $record
     *
     * @return array
     * @throws
     */
    protected function handleAnnotation(array $record): array
    {
        /**
         * preview annotation
         */

        $persistAnnotation = [];
        if ($this->entity) {
            $persistAnnotation = $this->web->getPersistenceAnnotation(
                $this->entity,
                [
                    'enumClass'          => $this->input->enum,
                    'doctrinePrefix'     => $this->web->parameter('doctrine_prefix'),
                    'doctrinePrefixMode' => $this->web->parameter('doctrine_prefix_mode'),
                ]
            );
        }

        /**
         * preview annotation only
         */

        $fn = self::ANNOTATION_ONLY;

        $arguments = $this->arguments(
            ['id' => $this->input->id, 'persistence' => !!$this->input->submit],
            compact('record')
        );
        $persistAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, null, $arguments);

        $arguments = $this->arguments(
            ['target' => $persistAnnotationExtra, 'id' => $this->input->id],
            compact('persistAnnotation', 'record')
        );
        $persistAnnotationExtra = $this->tailor($this->methodTailor, $fn, [Abs::T_ARRAY, null], $arguments);

        /**
         * extra annotation handler
         */

        if (!is_null($persistAnnotationExtra)) {

            $persistAnnotationOnlyKey = array_keys($persistAnnotationExtra);
            $persistAnnotation = Helper::arrayPull($persistAnnotation, $persistAnnotationOnlyKey);

        } else {

            /**
             * preview extra annotation
             */

            $fn = self::ANNOTATION;

            $arguments = $this->arguments(
                ['id' => $this->input->id, 'persistence' => !!$this->input->submit],
                compact('record')
            );
            $persistAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, [], $arguments);

            $arguments = $this->arguments(
                ['target' => $persistAnnotationExtra, 'id' => $this->input->id],
                compact('persistAnnotation', 'record')
            );
            $persistAnnotationExtra = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $arguments);
        }

        /**
         * annotation handler with extra
         */

        foreach ($persistAnnotationExtra as $field => $item) {

            $_item = $item;
            if (is_bool($item)) {
                $item = [];
            }

            if (!is_array($item)) {
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if ($_item === false) {
                $persistAnnotation[$field]['show'] = false;
            }

            if (isset($persistAnnotation[$field])) {
                $item = array_merge($persistAnnotation[$field], $item);
            }

            $original = $this->web->annotation(Persistence::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Persistence($item)]);
            $persistAnnotation[$field] = (array)current($item[Persistence::class]);
        }

        $persistAnnotation = Helper::sortArray($persistAnnotation, 'sort');

        /**
         * hooks
         */

        $hooks = [];
        $_persistAnnotation = $persistAnnotation;

        foreach ($persistAnnotation as $field => $item) {

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
                unset($persistAnnotation[$field]);
            }
        }

        return [$persistAnnotation, $_persistAnnotation, $hooks];
    }

    /**
     * Get persistence data
     *
     * @return ArgsOutput|array
     * @throws
     */
    protected function getPersistenceData()
    {
        if (empty($this->entity)) {
            return [[], [], [], [], []];
        }

        $key = "{$this->input->route}:record:before";

        /**
         * @var FoundationEntity $record
         */

        if ($this->input->submit) {

            $record = new $this->entity;
            [$submit, $extraSubmit] = $this->resolveSubmit($this->input->submit);

            $recordBefore = $this->web->sessionGet($key) ?? [];
            $recordDiff = Helper::arrayDiffAssoc($submit, $recordBefore);

            $args = compact('submit', 'extraSubmit', 'recordDiff', 'recordBefore');
            $arguments = $this->arguments($args, ['id' => $this->input->id]);
            $result = $this->caller(
                $this->method,
                self::AFTER_SUBMIT,
                [Message::class, Error::class, Abs::T_ARRAY],
                $args,
                $arguments
            );

            if ($result instanceof Error) {
                return $this->showError($result->tiny());
            } elseif ($result instanceof Message) {
                return $this->showMessage($result);
            } else {
                try {
                    [$submit, $extraSubmit] = array_values($result);
                } catch (Exception $e) {
                    $fn = $this->method . self::AFTER_SUBMIT;
                    throw new ModuleException(
                        "Method return illegal in {$this->class}::{$fn}():array, must return array with index 0 and 1"
                    );
                }
            }

            $args = compact('extraSubmit', 'recordDiff', 'recordBefore');
            $arguments = $this->arguments(
                ['target' => $submit],
                $args,
                ['id' => $this->input->id, 'default' => [$submit, $extraSubmit]]
            );
            $result = $this->tailor(
                $this->methodTailor,
                self::AFTER_SUBMIT,
                [Message::class, Error::class, Abs::T_ARRAY],
                $arguments
            );

            if ($result instanceof Error) {
                return $this->showError($result->tiny());
            } elseif ($result instanceof Message) {
                return $this->showMessage($result);
            } else {
                try {
                    [$submit, $extraSubmit] = array_values($result);
                } catch (Exception $e) {
                    $fn = $this->method . self::AFTER_SUBMIT;
                    throw new ModuleException(
                        "Method return illegal in Module\Tailor::{$fn}():array, must return array with index 0 and 1"
                    );
                }
            }

            if (!is_array($extraSubmit)) {
                throw new ModuleException('After submit handler extra should be return array');
            }

            $record->attributes($submit, true);
            $record = Helper::entityToArray($record);

            return [$submit, $record, $extraSubmit, $recordBefore, $recordDiff];
        }

        /**
         * Fetch data
         */

        if ($this->entity && $this->input->id) {
            $record = $this->repository->find($this->input->id);
        } else {
            $record = new $this->entity;
        }

        $record = Helper::entityToArray($record);
        $this->web->sessionSet($key, $record);

        return [[], $record, [], $record, $record];
    }

    /**
     * Set default value for form data
     *
     * @param Form   $form
     * @param string $field
     * @param array  $item
     * @param Output $output
     */
    protected function formDefaultConfigure(Form $form, string $field, array $item, Output $output)
    {
        if ($form instanceof Upload) {
            if (!$form->getRoute()) {
                $form->setRoute($this->input->cnf->route_upload);
            }

            $form->setUrl($this->web->urlSafe($form->getRoute(), $form->getArgs(), 'Upload route'));

            /**
             * File list key
             */
            $key = 'persistenceFileListKeyCollect';
            $form->setFileListKey("${key}.${field}.list");
            $form->setDisabled(!$this->web->routeIsAccess($form->getRouteForAccess()));
            $output->fileListKeyCollect[$field] = [
                'key'  => $key,
                'list' => [],
                'id'   => $field,
                'md5'  => $form->getFileMd5Key(),
                'sha1' => $form->getFileSha1Key(),
                'url'  => $form->getFileUrlKey(),
            ];

            /**
             * Upload tips
             */

            $option = $this->web->uploadOptionByFlag($form->getFlag());
            [$list, $suffix, $mime] = Uploader::optionTips(
                $option,
                function ($label) {
                    return $this->web->twigLang($label);
                }
            );

            $output->uploadTipsCollect[$field] = [
                'columns' => [
                    [
                        'title'     => $this->web->fieldLang('Type'),
                        'dataIndex' => 'type',
                        'align'     => 'right',
                        'width'     => 100,
                    ],
                    [
                        'title'     => $this->web->fieldLang('Condition'),
                        'dataIndex' => 'condition',
                        'width'     => 240,
                    ],
                ],
                'list'    => $list,
            ];

            /**
             * Accept
             */
            $accept = null;
            if ($suffix != '*' && strpos($suffix, '!') !== 0) {
                $accept .= ',.' . str_replace('、', ',.', $suffix);
            }
            if ($mime != '*' && strpos($mime, '!') !== 0) {
                $accept .= ',' . str_replace('、', ',', $mime);
            }

            if ($accept) {
                $form->setAccept(ltrim($accept, ','));
            }
        }

        if ($form instanceof Select) {
            if ($meta = $form->getSwitchFieldShape()) {
                $output->fieldShapeCollect[$field] = $meta;
            }
        }
    }

    /**
     * Get datetime format
     *
     * @param string $field
     * @param Form   $form
     * @param array  $format
     */
    protected function datetimeFormat(string $field, Form $form, array &$format = [])
    {
        if (Helper::extendClass($form, Datetime::class, true)) {

            /**
             * @var Datetime $form
             */
            $format[$field] = $form->getFormat();
        }

        if (Helper::extendClass($form, Group::class, true)) {
            /**
             * @var Group $form
             */
            foreach ($form->getMember() as $key => $groupForm) {
                $groupField = $groupForm->getField() ?? $key;
                $groupField = "{$field}_{$groupField}";
                $this->datetimeFormat($groupField, $groupForm, $format);
            }
        }
    }

    /**
     * Persistence data handler
     *
     * @param array  $persistAnnotation
     * @param array  $record
     * @param array  $hooks
     * @param Output $output
     *
     * @return array|ArgsOutput
     * @throws
     */
    protected function handlePersistenceData(array $persistAnnotation, array $record, array $hooks, Output $output)
    {
        /**
         * before hook (row record)
         *
         * @param array $original
         * @param array $extraArgs
         *
         * @return mixed
         */
        $before = function (array $original, array $extraArgs) {

            if (method_exists($this->web, $fn = $this->method . self::BEFORE_HOOK)) {
                $arguments = $this->arguments(compact('original', 'extraArgs'));
                $original = $this->web->{$fn}($arguments);
            }

            $arguments = $this->arguments(
                ['target' => $original],
                compact('extraArgs')
            );

            return $this->tailor($this->methodTailor, self::BEFORE_HOOK, Abs::T_ARRAY, $arguments);
        };

        /**
         * after hook (row record)
         *
         * @param array $hooked
         * @param array $original
         * @param array $extraArgs
         *
         * @return mixed
         */
        $after = function (array $hooked, array $original, array $extraArgs) {

            if (method_exists($this->web, $fn = $this->method . self::AFTER_HOOK)) {
                $arguments = $this->arguments(compact('hooked', 'original', 'extraArgs'));
                $hooked = $this->web->{$fn}($arguments);
            }

            $arguments = $this->arguments(
                ['target' => $hooked],
                compact('original', 'extraArgs')
            );

            return $this->tailor($this->methodTailor, self::AFTER_HOOK, Abs::T_ARRAY, $arguments);
        };

        $persistence = !!$this->input->submit;
        $extraArgs = [Abs::HOOKER_FLAG_ACME => ['scene' => 'persistence_' . ($this->input->id ? 'modify' : 'newly')]];
        $_hooks = [];
        foreach ($hooks as $hook => $item) {
            $_hooks[$hook] = $item['fields'];
            $extraArgs[$hook] = array_merge($extraArgs[$hook] ?? [], $item['args']);
        }

        $original = $record;
        $record = $this->web->hooker($_hooks, $record, $persistence, $before, $after, $extraArgs);
        $hooked = $record;

        if ($persistence) {
            return [$record, [], [], $original];
        }

        /**
         * before render (all record)
         */

        $args = array_merge(compact('hooked', 'original', 'persistence'), ['id' => $this->input->id]);

        $arguments = $this->arguments($args);
        $record = $this->caller(
            $this->method,
            self::BEFORE_RENDER,
            [Message::class, Error::class, Abs::T_ARRAY],
            $hooked,
            $arguments
        );
        if ($record instanceof Error) {
            return $this->showError($record->tiny());
        } elseif ($record instanceof Message) {
            return $this->showMessage($record);
        }

        $arguments = $this->arguments(['target' => $record], $args);
        $record = $this->tailor(
            $this->methodTailor,
            self::BEFORE_RENDER,
            [Message::class, Error::class, Abs::T_ARRAY],
            $arguments
        );
        if ($record instanceof Error) {
            return $this->showError($record->tiny());
        } elseif ($record instanceof Message) {
            return $this->showMessage($record);
        }

        $_record = [];
        $format = [];

        foreach ($persistAnnotation as $field => $item) {

            /**
             * @var Form $form
             */
            $form = $item['type'];
            $label = $item['label'];

            $form->setDisabled($item['disabled']);
            $form->setStyle($item['style']);

            foreach ($item['rules'] as $key => &$rule) {
                if (!is_array($rule) || !$rule['message']) {
                    unset($item['rules'][$key]);
                } else {
                    $args = ['{{ field }}' => $this->web->fieldLang($label)];
                    $args = array_merge($args, $rule['args'] ?? []);
                    $rule['message'] = $this->web->messageLang($rule['message'], $args);
                }
            }

            $form->setRules($item['rules']);
            if (isset($record[$field])) {
                $form->setValue($record[$field]);
            }

            if (isset($item['value'])) {
                $form->setValue($item['value']);
            }

            if (method_exists($form, 'setSize')) {
                $form->setSize($this->input->formSize);
            }

            /**
             * extra enum
             */

            $item = $this->handleForEnum($item, ['scene' => $this->input->scene, 'id' => $this->input->id]);

            $enumClass = [
                Select::class,
                Radio::class,
                Checkbox::class,
            ];

            if (in_array(get_class($form), $enumClass)) {

                if (!is_array($item['enum'])) {
                    $exception = $this->getAnnotationException($field);
                    $enumClassStr = implode("\n", $enumClass);
                    throw new AnnotationException(
                        "{$exception} option `enum` must configure when type is below:\n\n{$enumClassStr}"
                    );
                }

                /**
                 * @var Select $form
                 */
                $form->setEnum($this->web->enumLang($item['enum']));
            }

            $this->datetimeFormat($field, $form, $format);

            if (!$form->getPlaceholder()) {
                $form->setPlaceholder($item['placeholder'] ?: $label);
            }

            $this->formDefaultConfigure($form, $field, $item, $output);

            $tipsAuto = $titleAuto = null;
            if (get_class($form) == Select::class && $form->getMode() == Select::MODE_MULTIPLE) {
                if (!$this->input->id || $form->isValueMultiple()) {
                    $tipsAuto = $this->web->twigLang('For multiple newly');
                } else {
                    $form->setMode(Select::MODE_DEFAULT);
                }
            }

            if (in_array(JsonStringify::class, $item['hook'])) {
                $button = (new Button('Verify JSON format'))
                    ->setIcon($this->input->cnf->icon_badge)
                    ->setType(Button::THEME_LINK)
                    ->setSize(Button::SIZE_SMALL)
                    ->setClick('verifyJsonFormat')
                    ->setArgs(['field' => $field, 'url' => $this->input->cnf->verify_json_url, 'key' => 'json']);
                $titleAuto = $this->web->getButtonHtml($button, true);
            }

            $_record[$field] = [
                'hide'      => $item['hide'],
                'label'     => $item['trans'] ? $this->web->fieldLang($label) : $label,
                'tips'      => $item['tips'],
                'tipsAuto'  => $tipsAuto,
                'title'     => $item['title'],
                'titleAuto' => $titleAuto,
                'column'    => $item['column'],
                'type'      => $form,
            ];
        }

        $submit = new Button('Submit', $this->input->route, $this->input->cnf->icon_sure);
        $submit->setArgs(['id' => $this->input->id]);
        $submit->setAttributes(['bsw-method' => 'submit']);

        $arguments = $this->arguments(compact('submit', 'record', 'hooked', 'original'), ['id' => $this->input->id]);
        $operates = $this->caller($this->method, self::FORM_OPERATE, Abs::T_ARRAY, [], $arguments);
        $operates = array_merge(['submit' => $submit], $operates);
        $operates = array_filter($operates);

        foreach ($operates as $operate) {

            $buttonCls = Button::class;
            if (!Helper::extendClass($operate, $buttonCls, true)) {
                $fn = self::FORM_OPERATE;
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
            }

            /**
             * @var Button $operate
             */

            $operate->setClick('setUrlToForm');
            $operate->setScript(Html::scriptBuilder($operate->getClick(), $operate->getArgs()));
            $operate->setUrl($this->web->urlSafe($operate->getRoute(), $operate->getArgs(), 'Persistence button'));

            $operate->setHtmlType(Button::TYPE_SUBMIT);
            $operate->setDisabled(!$this->web->routeIsAccess($operate->getRouteForAccess()));
        }

        return [$_record, $operates, $format, $original];
    }

    /**
     * Resolve submit
     *
     * @param array $submit
     *
     * @return array
     */
    protected function resolveSubmit(array $submit): array
    {
        $extraSubmit = [];
        $document = $this->entityDocument();
        foreach ($submit as $field => $value) {
            $_field = Helper::camelToUnder($field);
            if (!isset($document[$_field])) {
                $extraSubmit[$field] = $value;
                unset($submit[$field]);
                continue;
            }
        }

        return [$submit, $extraSubmit];
    }

    /**
     * Record handler
     *
     * @param array  $submit
     * @param array  $record
     * @param array  $_persistAnnotation
     * @param array  $extraSubmit
     * @param string $multipleField
     *
     * @return array
     */
    protected function recordHandler(
        array $submit,
        array $record,
        array $_persistAnnotation,
        array $extraSubmit,
        string $multipleField = null
    ): array {

        $document = $this->entityDocument();
        foreach ($record as $field => $value) {

            $_field = Helper::camelToUnder($field);

            // Field don't exists
            if (!isset($_persistAnnotation[$field])) {
                unset($record[$field]);
                continue;
            }

            // Force ignore
            if ($_persistAnnotation[$field]['ignore']) {
                unset($record[$field]);
                continue;
            }

            // No show and not in submit
            if (!$_persistAnnotation[$field]['show'] && !isset($submit[$field])) {
                unset($record[$field]);
                continue;
            }

            // When field is null but default is not null
            if (is_null($value) && $document[$_field]['default'] !== null) {
                unset($record[$field]);
                continue;
            }

            // Ignore when blank
            if ($_persistAnnotation[$field]['ignoreBlank'] && trim($value) === '') {
                unset($record[$field]);
                continue;
            }
        }

        foreach ($extraSubmit as $field => $value) {

            $_field = Helper::camelToUnder($field);

            // Field not in annotation
            if (!isset($_persistAnnotation[$field])) {
                unset($extraSubmit[$field]);
                continue;
            }

            // Field not in entity
            if (!isset($document[$_field])) {
                unset($extraSubmit[$field]);
                continue;
            }
        }

        $record = array_merge($record, $extraSubmit);
        $recordClean = Html::cleanArrayHtml($record);

        /**
         * Select use multiple mode
         */
        $multiple = false;
        $recordList = [$record];
        $recordCleanList = [$recordClean];

        if (isset($record[$multipleField])) {
            $multiple = true;
            $recordList = $recordCleanList = [];
            foreach ($record[$multipleField] as $item) {
                $record[$multipleField] = $item;
                array_push($recordList, $record);
            }
            foreach ($recordClean[$multipleField] as $item) {
                $recordClean[$multipleField] = $item;
                array_push($recordCleanList, $recordClean);
            }
        }

        /**
         * Handler by validator type
         */
        foreach ($recordList as $key => $item) {
            foreach ($item as $field => $value) {

                if (!$_persistAnnotation[$field]['html']) {
                    $value = $recordCleanList[$key][$field];
                }

                /**
                 * validator type
                 */
                $type = $_persistAnnotation[$field]['validatorType'];
                if (strpos($type, 'int') !== false) {
                    $recordList[$key][$field] = intval($value);
                } elseif (!is_null($value)) {
                    $recordList[$key][$field] = strval($value);
                }
            }
        }

        return $multiple ? $recordList : current($recordList);
    }

    /**
     * Persistence to MySQL
     *
     * @param array $submit
     * @param array $record
     * @param array $original
     * @param array $_persistAnnotation
     * @param array $extraSubmit
     * @param array $recordBefore
     * @param array $recordDiff
     *
     * @return Output
     * @throws
     */
    protected function persistence(
        array $submit,
        array $record,
        array $original,
        array $_persistAnnotation,
        array $extraSubmit,
        array $recordBefore,
        array $recordDiff
    ): ArgsOutput {

        if (empty($this->entity)) {
            throw new ModuleException('Entity is required for persistence module');
        }

        $pk = $this->repository->pk();
        $newly = empty($record[$pk]);

        $result = $this->repository->transactional(
            function () use (
                $submit,
                $record,
                $original,
                $_persistAnnotation,
                $extraSubmit,
                $recordBefore,
                $recordDiff,
                $newly,
                $pk
            ) {

                /**
                 * Before persistence
                 */

                $arguments = $this->arguments(
                    compact('newly', 'record', 'original', 'extraSubmit', 'recordBefore', 'recordDiff')
                );

                $before = $this->caller(
                    $this->method,
                    self::BEFORE_PERSISTENCE,
                    [Message::class, Error::class, true],
                    null,
                    $arguments
                );

                if ($before instanceof Error) {
                    throw new LogicException($before->tiny());
                }

                if (($before instanceof Message) && $before->isErrorClassify()) {
                    throw new LogicException($before->getMessage());
                }

                if ($newly) {

                    /**
                     * Newly record
                     */

                    $multipleField = null;
                    foreach ($_persistAnnotation as $field => $item) {
                        if (is_array($record[$field] ?? null)) {
                            $multipleField = $field;
                            break; // multiple allow one only
                        }
                    }

                    $loggerType = $multipleField ? 2 : 1;
                    $recordBefore = $recordDiff = [];
                    $record = $this->recordHandler($record, $record, $_persistAnnotation, $extraSubmit, $multipleField);

                    if ($multipleField) {
                        $result = $this->repository->newlyMultiple($record);
                    } else {
                        $result = $this->repository->newly($record);
                    }

                } else {

                    /**
                     * Modify by id
                     */

                    $loggerType = 3;
                    $record = $this->recordHandler($submit, $record, $_persistAnnotation, $extraSubmit);
                    $result = $this->repository->modify([$pk => Helper::dig($record, $pk)], $record);
                }

                if ($result === false) {
                    [$error, $flag] = $this->repository->pop(true);
                    if (strpos($flag, Abs::TAG_ROLL) === 0) {
                        throw new LogicException($error);
                    } elseif (strpos($flag, Abs::TAG_VALIDATOR) !== false) {
                        throw new ValidatorException($error);
                    } else {
                        throw new RepositoryException($error);
                    }
                }

                $recordDiff['__effect'] = $result;
                $record['__extra'] = $extraSubmit;
                $this->web->databaseOperationLogger($this->entity, $loggerType, $recordBefore, $record, $recordDiff);

                /**
                 * After persistence
                 */

                $arguments = $this->arguments(
                    compact('newly', 'record', 'original', 'extraSubmit', 'recordBefore', 'recordDiff', 'result')
                );

                $after = $this->caller(
                    $this->method,
                    self::AFTER_PERSISTENCE,
                    [Message::class, Error::class, true],
                    null,
                    $arguments
                );

                if ($after instanceof Error) {
                    throw new LogicException($after->tiny());
                }

                if (($after instanceof Message) && $after->isErrorClassify()) {
                    throw new LogicException($after->getMessage());
                }

                return [$result, $before, $after];
            }
        );

        /**
         * Handle error
         */
        if ($result === false) {
            return $this->showError($this->repository->pop());
        }

        [$result, $before, $after] = $result;

        return $this->showSuccess(
            $newly ? $this->input->i18nNewly : $this->input->i18nModify,
            $this->input->sets,
            [
                '{{ result }}' => $result,
                '{{ before }}' => $before,
                '{{ after }}'  => $after,
            ],
            isset($this->input->sets['function']) ? null : $this->input->nextRoute
        );
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

        $result = $this->getPersistenceData();
        if ($result instanceof ArgsOutput) {
            return $result;
        } else {
            [$submit, $record, $extraSubmit, $recordBefore, $recordDiff] = $result;
        }

        // get annotation
        [$persistAnnotation, $_persistAnnotation, $hooks] = $this->handleAnnotation($record);

        $result = $this->handlePersistenceData($persistAnnotation, $record, $hooks, $output);
        if ($result instanceof ArgsOutput) {
            return $result;
        } else {
            [$record, $operates, $format, $original] = $result;
        }

        if ($this->input->submit) {
            if ($this->entity) {
                return $this->persistence(
                    $submit,
                    $record,
                    $original,
                    $_persistAnnotation,
                    $extraSubmit,
                    $recordBefore,
                    $recordDiff
                );
            }

            if (empty($this->input->handler) || !is_callable($this->input->handler)) {
                throw new Exception(
                    "Persistence handler should be configured and callable when entity not configured"
                );
            }

            $result = call_user_func_array(
                $this->input->handler,
                [$this->input->submit, $_persistAnnotation]
            );

            if ($result instanceof Error) {
                return $this->showError($result->tiny());
            }

            return $this->showMessage($result);
        }

        /**
         * assign variable to output
         */

        $fillData = $this->web->getArgs($this->input->fill) ?? [];
        $fillData = Helper::numericValues($fillData);

        foreach ($record as $key => $item) {
            /**
             * @var Form $form
             */
            $form = $item['type'];
            if (isset($fillData[$key])) {
                $form->setValue($fillData[$key]);
            }
            if (get_class($form) == CkEditor::class) {
                $this->web->appendSrcJs([Abs::JS_EDITOR_LANG[$this->web->langLatest($this->web->langMap)]]);
                $this->web->appendSrcJsWithKey('ck-editor', Abs::JS_EDITOR);
                $this->web->appendSrcJsWithKey('ck-editor-custom', Abs::JS_EDITOR_CUSTOM);
                $this->web->appendSrcCssWithKey('ck-editor', Abs::CSS_EDITOR);
            }
        }

        $output->id = $this->input->id;
        $output->record = $record;
        $output->operates = $operates;
        $output->formatJson = Helper::jsonStringify($format, '{}');

        $output->fileListKeyCollectJson = Helper::jsonStringify($output->fileListKeyCollect, '{}');
        $output->uploadTipsCollectJson = Helper::jsonStringify($output->uploadTipsCollect, '{}');
        $output->fieldShapeCollectJson = Helper::jsonStringify($output->fieldShapeCollect, '{}');

        $output->style = array_merge($output->style, $this->input->style);
        $output->styleJson = Helper::jsonStringify($output->style, '{}');

        $output = $this->caller(
            $this->method,
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}