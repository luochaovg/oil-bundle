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
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Radio;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Module\Form\Entity\TextArea;
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
    const BEFORE_HOOK        = 'BeforeHook';        // 钩子前处理
    const AFTER_HOOK         = 'AfterHook';         // 钩子后处理
    const BEFORE_RENDER      = 'BeforeRender';      // 渲染前处理
    const FORM_OPERATE       = 'FormOperates';      // 操作按钮
    const AFTER_SUBMIT       = 'AfterSubmit';       // 提交数据后处理
    const BEFORE_PERSISTENCE = 'BeforePersistence'; // 持久化前处理 (事务级)
    const AFTER_PERSISTENCE  = 'AfterPersistence';  // 持久化后处理 (事务级)

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
     * @return array
     * @throws
     */
    protected function handleAnnotation(): array
    {
        /**
         * preview annotation
         */

        $persistAnnotation = [];
        if ($this->entity) {
            $persistAnnotation = $this->web->getPersistenceAnnotation(
                $this->entity,
                [
                    'enumClass'      => $this->input->enum,
                    'doctrinePrefix' => $this->web->parameter('doctrine_prefix'),
                ]
            );
        }

        /**
         * preview annotation only
         */

        $fn = self::ANNOTATION_ONLY;
        $id = $this->input->id;

        $persistAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, null, [$id]);
        $persistAnnotationExtra = $this->tailor(
            $this->methodTailor,
            $fn,
            null,
            $persistAnnotationExtra,
            $persistAnnotation,
            $id
        );

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
            $persistAnnotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, [], [$id]);
            $persistAnnotationExtra = $this->tailor(
                $this->methodTailor,
                $fn,
                Abs::T_ARRAY,
                $persistAnnotationExtra,
                $persistAnnotation,
                $id
            );
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

            foreach ($item['hook'] as $hook) {
                $hooks[$hook][] = $field;
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
            return [[], [], [], []];
        }

        $key = "{$this->input->route}:record:before";

        /**
         * @var FoundationEntity $record
         */

        if ($this->input->submit) {

            $record = new $this->entity;
            $submit = $this->input->submit;

            $recordBefore = $this->web->sessionGet($key) ?? [];
            $recordDiff = Helper::arrayDiffAssoc($submit, $recordBefore);

            try {

                $args = [$submit, [], $recordDiff, $recordBefore];
                $argsItem = array_merge($args, [$this->input->id]);
                $result = $this->caller($this->method, self::AFTER_SUBMIT, null, $args, $argsItem);

                if ($result instanceof Error) {
                    return $this->showError($result->tiny());
                } elseif ($result instanceof Message) {
                    return $this->showMessage($result);
                } else {
                    [$submit, $extraSubmit] = $result;
                }

            } catch (Exception $e) {
                $fn = $this->method . self::AFTER_SUBMIT;
                throw new ModuleException(
                    "Method return illegal in {$this->class}::{$fn}():array, must return array with index 0 and 1"
                );
            }

            try {

                $args = [$submit, $extraSubmit, $recordDiff, $recordBefore];
                $result = $this->tailor($this->methodTailor, self::AFTER_SUBMIT, null, $args, $this->input->id);

                if ($result instanceof Error) {
                    return $this->showError($result->tiny());
                } elseif ($result instanceof Message) {
                    return $this->showMessage($result);
                } else {
                    [$submit, $extraSubmit] = $result;
                }

            } catch (Exception $e) {
                $fn = $this->method . self::AFTER_SUBMIT;
                throw new ModuleException(
                    "Method return illegal in Module\Tailor::{$fn}():array, must return array with index 0 and 1"
                );
            }

            if (!is_array($extraSubmit)) {
                throw new ModuleException('After submit handler should be return array');
            }

            $this->input->submit = array_merge($submit, $extraSubmit);
            $record->attributes($this->input->submit, true);
            $record = Helper::entityToArray($record);

            return [$record, $extraSubmit, $recordBefore, $recordDiff];
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

        return [$record, [], $record, $record];
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
            $key = 'persistence_file_list_key_collect';
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
            $trans = $this->input->translator;
            $option = $this->web->uploadOptionByFlag($form->getFlag());
            [$list, $suffix, $mime] = Uploader::optionTips(
                $option,
                function ($label) use ($trans) {
                    return $trans->trans($label, [], 'twig');
                }
            );

            $output->uploadTipsCollect[$field] = [
                'columns' => [
                    [
                        'title'     => $trans->trans('Type', [], 'fields'),
                        'dataIndex' => 'type',
                        'align'     => 'right',
                        'width'     => 100,
                    ],
                    [
                        'title'     => $trans->trans('Condition', [], 'fields'),
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
    }

    /**
     * Persistence data handler
     *
     * @param array  $persistAnnotation
     * @param array  $record
     * @param array  $hooks
     * @param Output $output
     *
     * @return array
     * @throws
     */
    protected function handlePersistenceData(
        array $persistAnnotation,
        array $record,
        array $hooks,
        Output $output
    ): array {

        /**
         * before hook (row record)
         */

        $before = null;
        if (method_exists($this->web, $fn = $this->method . self::BEFORE_HOOK)) {
            $before = [$this->web, $fn];
        }

        /**
         * after hook (row record)
         */

        $after = null;
        if (method_exists($this->web, $fn = $this->method . self::AFTER_HOOK)) {
            $after = [$this->web, $fn];
        }

        $persistence = !!$this->input->submit;
        $extraArgs = ['_acme' => ['scene' => 'persistence_' . ($this->input->id ? 'modify' : 'newly')]];

        $original = $record;
        $record = $this->web->hooker($hooks, $record, $persistence, $before, $after, $extraArgs);
        $hooked = $record;

        if ($persistence) {
            return [$record, [], [], $original];
        }

        /**
         * before render (all record)
         */

        $args = [$hooked, $original, $persistence, $this->input->id];

        $record = $this->caller($this->method, self::BEFORE_RENDER, Abs::T_ARRAY, $hooked, $args);
        $record = $this->tailor($this->methodTailor, self::BEFORE_RENDER, Abs::T_ARRAY, $record, ...$args);

        $_record = [];
        $format = [];
        $trans = $this->input->translator;

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
                    $args = ['{{ field }}' => $trans->trans($label, [], 'fields')];
                    $args = array_merge($args, $rule['args'] ?? []);
                    $rule['message'] = $trans->trans($rule['message'], $args, 'messages');
                }
            }

            $form->setRules($item['rules']);
            if (isset($record[$field])) {
                $form->setValue($record[$field]);
            }

            if (isset($item['value'])) {
                $form->setValue($item['value']);
            }

            /**
             * extra enum
             */

            $item = $this->handleForEnum($item, [$this->input->id]);

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

            if (Helper::extendClass($form, Datetime::class, true)) {

                /**
                 * @var Datetime $form
                 */
                $format[$field] = $form->getFormat();
            }

            if (!$form->getPlaceholder()) {
                $form->setPlaceholder($item['placeholder'] ?: $label);
            }

            $this->formDefaultConfigure($form, $field, $item, $output);

            $tipsAuto = $titleAuto = null;
            if (get_class($form) == Select::class && $form->getMode() == Select::MODE_MULTIPLE && !$this->input->id) {
                $tipsAuto = $this->input->translator->trans('For multiple newly', [], 'twig');
            }

            if (in_array(JsonStringify::class, $item['hook'])) {
                $button = (new Button('Verify JSON format'))
                    ->setIcon($this->input->cnf->icon_badge)
                    ->setType(Button::THEME_LINK)
                    ->setSize(Button::SIZE_SMALL)
                    ->setClick('verifyJsonFormat')
                    ->setArgs(['field' => $field, 'url' => Abs::VERIFY_JSON, 'key' => 'json']);
                $titleAuto = $this->web->renderPart('@LeonBsw/form/button', ['form' => $button]);
            }

            $_record[$field] = [
                'hide'      => $item['hide'],
                'label'     => $item['trans'] ? $this->web->labelLang($label) : $label,
                'tips'      => $item['tips'],
                'tipsAuto'  => $tipsAuto,
                'title'     => $item['title'],
                'titleAuto' => $titleAuto,
                'column'    => $item['column'],
                'type'      => $form,
            ];
        }

        $submit = new Button('Submit', $this->input->route, 'a:coffee');
        $submit->setAttributes(['bsw-method' => 'submit']);

        $args = [$record, $hooked, $original, $this->input->id, $submit];
        $operates = $this->caller($this->method, self::FORM_OPERATE, Abs::T_ARRAY, [], $args);
        $operates = array_merge(['submit' => $submit], $operates);

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
     * Record handler
     *
     * @param array  $record
     * @param array  $_persistAnnotation
     * @param array  $extraSubmit
     * @param string $multipleField
     *
     * @return array
     */
    protected function recordHandler(
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

            // Ignore strict
            if ($_persistAnnotation[$field]['ignore']) {
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

            // Field don't exists
            if (!isset($_persistAnnotation[$field])) {
                unset($extraSubmit[$field]);
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
                $newly,
                $_persistAnnotation,
                $record,
                $original,
                $extraSubmit,
                $pk,
                $recordBefore,
                $recordDiff
            ) {

                /**
                 * Before persistence
                 */

                $before = $this->caller(
                    $this->method,
                    self::BEFORE_PERSISTENCE,
                    [Message::class, Error::class, true],
                    null,
                    [$newly, $record, $original]
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

                    $multiple = null;
                    foreach ($_persistAnnotation as $field => $item) {
                        /**
                         * @var Form $type
                         */
                        $type = $item['type'];
                        if (get_class($type) == Select::class && $type->getMode() == Select::MODE_MULTIPLE) {
                            $multiple = $field;
                            break;
                        }
                    }

                    $loggerType = $multiple ? 2 : 1;
                    $recordBefore = $recordDiff = [];
                    $record = $this->recordHandler($record, $_persistAnnotation, $extraSubmit, $multiple);

                    if ($multiple) {
                        $result = $this->repository->newlyMultiple($record);
                    } else {
                        $result = $this->repository->newly($record);
                    }

                } else {

                    /**
                     * Modify by id
                     */

                    $loggerType = 3;
                    $record = $this->recordHandler($record, $_persistAnnotation, $extraSubmit);
                    $result = $this->repository->modify([$pk => Helper::dig($record, $pk)], $record);
                }

                if ($result === false) {
                    [$error, $flag] = $this->repository->pop(true);
                    if (strpos($flag, Abs::TAG_ROLL) === 0) {
                        throw new LogicException($error);
                    } else {
                        throw new RepositoryException($error);
                    }
                }

                $recordDiff['__effect'] = $result;
                $this->web->databaseOperationLogger($this->entity, $loggerType, $recordBefore, $record, $recordDiff);

                /**
                 * After persistence
                 */

                $after = $this->caller(
                    $this->method,
                    self::AFTER_PERSISTENCE,
                    [Message::class, Error::class, true],
                    null,
                    [$newly, $record, $original, $result]
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

        return $this->showSuccess($newly ? $this->input->i18nNewly : $this->input->i18nModify);
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

        [$persistAnnotation, $_persistAnnotation, $hooks] = $this->handleAnnotation();

        $result = $this->getPersistenceData();
        if ($result instanceof ArgsOutput) {
            return $result;
        } else {
            [$record, $extraSubmit, $recordBefore, $recordDiff] = $result;
        }

        [$record, $operates, $format, $original] = $this->handlePersistenceData(
            $persistAnnotation,
            $record,
            $hooks,
            $output
        );

        if ($this->input->submit) {
            if ($this->entity) {
                return $this->persistence(
                    $record,
                    $original,
                    $_persistAnnotation,
                    $extraSubmit,
                    $recordBefore,
                    $recordDiff
                );
            } else {

                if (empty($this->input->handler) || !is_callable($this->input->handler)) {
                    throw new Exception(
                        "Persistence handler should be configured and callable when entity not configured"
                    );
                }

                $message = call_user_func_array(
                    $this->input->handler,
                    [$this->input->submit, $_persistAnnotation, $extraSubmit]
                );

                if ($message instanceof Error) {
                    return $this->showError($message->tiny());
                }

                return $this->showMessage($message);
            }
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
            if (get_class($form) == TextArea::class && strpos($form->getClass(), 'bsw-ck-editor') !== false) {
                $this->web->appendSrcJsWithKey('ck-editor', Abs::JS_EDITOR);
                $this->web->appendSrcJsWithKey('ck-editor-custom', Abs::JS_EDITOR_CUSTOM);
                $this->web->appendSrcCssWithKey('ck-editor', Abs::CSS_EDITOR);
            }
        }

        $output->id = $this->input->id;
        $output->record = $record;
        $output->operates = $operates;
        $output->formatJson = $this->json($format);

        $output->fileListKeyCollectJson = $this->json($output->fileListKeyCollect);
        $output->uploadTipsCollectJson = $this->json($output->uploadTipsCollect);

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