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
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Radio;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Module\Form\Entity\Upload;
use Leon\BswBundle\Module\Form\Form;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
    const BEFORE_HOOK   = 'BeforeHook';        // 钩子前处理
    const AFTER_HOOK    = 'AfterHook';         // 钩子后处理
    const BEFORE_RENDER = 'BeforeRender';      // 渲染前处理
    const FORM_OPERATE  = 'FormOperates';      // 操作按钮
    const AFTER_SUBMIT  = 'AfterSubmit';       // 提交后处理
    const WHEN_PERSIST  = 'WhenPersist';       // 与持久化同时处理（事务级）

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

        $annotation = [];
        if ($this->entity) {
            $annotation = $this->web->getPersistenceAnnotation($this->entity, $this->input->enum);
        }

        /**
         * preview extra annotation
         */

        $fn = self::ANNOTATION;
        $annotationExtra = $this->caller($this->method, $fn, Abs::T_ARRAY, [], [$this->input->id]);
        $annotationExtra = $this->tailor(
            $this->methodTailor,
            $fn,
            Abs::T_ARRAY,
            $annotationExtra,
            $annotation,
            $this->input->id
        );

        /**
         * annotation handler with extra
         */

        foreach ($annotationExtra as $field => $item) {

            if ($item === false) {
                $item = [];
            }

            if (!is_array($item)) {
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if (empty($item)) {
                $annotation[$field]['show'] = false;
            }

            if (isset($annotation[$field])) {
                $item = array_merge($annotation[$field], $item);
            }

            $original = $this->web->annotation(Persistence::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Persistence($item)]);
            $annotation[$field] = (array)current($item[Persistence::class]);
        }

        $annotation = Helper::sortArray($annotation, 'sort');

        /**
         * hooks
         */

        $hooks = [];
        $_annotation = $annotation;

        foreach ($annotation as $field => $item) {

            foreach ($item['hook'] as $hook) {
                $hooks[$hook][] = $field;
            }

            if (!$item['show']) {
                unset($annotation[$field]);
            }
        }

        return [$annotation, $_annotation, $hooks];
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
            return [[], []];
        }

        /**
         * @var FoundationEntity $record
         */

        if (!empty($this->input->submit)) {

            $record = new $this->entity;
            $submit = $this->input->submit;

            try {

                $args = [$submit, []];
                $argsItem = array_merge($args, [$this->input->id]);
                $result = $this->caller($this->method, self::AFTER_SUBMIT, null, $args, $argsItem);

                if ($result instanceof Error) {
                    return $this->showError($result->tiny());
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

                $args = [$submit, $extraSubmit];
                $result = $this->tailor($this->methodTailor, self::AFTER_SUBMIT, null, $args, $this->input->id);

                if ($result instanceof Error) {
                    return $this->showError($result->tiny());
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

            return [Helper::entityToArray($record), $extraSubmit];
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

        return [$record, []];
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

            try {
                $form->setUrl($this->web->url($form->getRoute(), $form->getArgs(), false));
            } catch (RouteNotFoundException $e) {
                $this->input->logger->warning("Upload route error, {$e->getMessage()}");
            }

            $key = "{$field}_{$this->input->uuid}__FileList";
            $form->setFileListKey($key);
            $form->setDisabled(!$this->web->routeIsAccess($form->getRouteForAccess()));
            $output->dataKeys[$key] = '[]';

            if ($md5 = $form->getFileMd5Key()) {
                $form->setFileMd5Key("{$md5}_{$this->input->uuid}");
            }

            if ($sha1 = $form->getFileSha1Key()) {
                $form->setFileSha1Key("{$sha1}_{$this->input->uuid}");
            }
        }
    }

    /**
     * Persistence data handler
     *
     * @param array  $annotation
     * @param array  $record
     * @param array  $hooks
     * @param Output $output
     *
     * @return array
     * @throws
     */
    protected function handlePersistenceData(array $annotation, array $record, array $hooks, Output $output): array
    {
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

        $persistence = !empty($this->input->submit);

        $original = $record;
        $record = $this->web->hooker($hooks, $record, $persistence, $before, $after);
        $hooked = $record;

        if ($persistence) {
            return [$record, [], []];
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

        foreach ($annotation as $field => $item) {

            /**
             * @var Form $form
             */
            $form = $item['type'];
            $label = $item['label'];

            $form->setDisabled($item['disabled']);
            $form->setStyle($item['style']);

            foreach ($item['rules'] as &$message) {
                $message = $trans->trans(
                    $message,
                    ['{{ field }}' => $trans->trans($label, [], 'fields')],
                    'messages'
                );
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

            $_record[$field] = [
                'hide'   => $item['hide'],
                'label'  => $item['trans'] ? $this->web->labelLang($label) : $label,
                'tips'   => $item['tips'],
                'column' => $item['column'],
                'type'   => $form,
                'title'  => $item['title'],
            ];
        }

        $submit = new Button('Submit', $this->input->route, 'a:coffee');
        $submit->setAttributes(['bsw-method' => 'submit']);

        $args = [$record, $hooked, $original, $this->input->id];
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

            try {
                $operate->setUrl($this->web->url($operate->getRoute(), $operate->getArgs(), false));
            } catch (RouteNotFoundException $e) {
                $this->input->logger->warning("Persistence button route error, {$e->getMessage()}");
            }

            $operate->setHtmlType(Button::TYPE_SUBMIT);
            $operate->setDisabled(!$this->web->routeIsAccess($operate->getRouteForAccess()));
        }

        return [$_record, $operates, $format];
    }

    /**
     * Record handler
     *
     * @param array $record
     * @param array $annotation
     * @param array $extraSubmit
     *
     * @return array
     */
    protected function recordHandler(array $record, array $annotation, array $extraSubmit): array
    {
        foreach ($record as $field => $value) {

            // Field don't exists
            if (!isset($annotation[$field])) {
                unset($record[$field]);
                continue;
            }

            // Ignore strict
            if ($annotation[$field]['ignore']) {
                unset($record[$field]);
                continue;
            }

            // When field is allow null and it's unique key
            if (is_null($value)) {
                unset($record[$field]);
                continue;
            }

            // Ignore when blank
            if ($annotation[$field]['ignoreBlank'] && trim($value) === '') {
                unset($record[$field]);
                continue;
            }
        }

        foreach ($extraSubmit as $field => $value) {

            // Field don't exists
            if (!isset($annotation[$field])) {
                unset($extraSubmit[$field]);
            }
        }

        $record = array_merge($record, $extraSubmit);
        $recordClean = Html::cleanArrayHtml($record);

        /**
         * Handler by validator type
         */
        foreach ($record as $field => &$value) {

            if (!$annotation[$field]['html']) {
                $value = $recordClean[$field];
            }

            /**
             * @var Form $form
             */
            // $form = $annotation[$field]['type'];

            /**
             * validator type
             */
            $type = $annotation[$field]['validatorType'];
            if (strpos($type, 'int') !== false) {
                $value = intval($value);
            } else {
                $value = strval($value);
            }
        }

        return $record;
    }

    /**
     * Persistence to MySQL
     *
     * @param array $record
     * @param array $annotation
     * @param array $extraSubmit
     *
     * @return Output
     * @throws
     */
    protected function persistence(array $record, array $annotation, array $extraSubmit): ArgsOutput
    {
        if (empty($this->entity)) {
            throw new ModuleException('Entity is required for persistence module');
        }

        $pk = $this->repository->pk();
        $newly = empty($record[$pk]);

        if ($newly) {

            /**
             * Newly
             */

            // TODO
            $record = $this->recordHandler($record, $annotation, $extraSubmit);
            $result = $id = $this->repository->newly($record);

        } else {

            /**
             * Modify by id
             */

            $record = $this->recordHandler($record, $annotation, $extraSubmit);
            $result = $this->repository->modify([$pk => Helper::dig($record, $pk)], $record);
        }

        /**
         * Handle error
         */
        if ($result === false) {
            return $this->showError($this->repository->pop());
        }

        return $this->showSuccess($newly ? 'Newly record done' : 'Modify record done');
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

        [$annotation, $_annotation, $hooks] = $this->handleAnnotation();

        $result = $this->getPersistenceData();
        if ($result instanceof ArgsOutput) {
            return $result;
        } else {
            [$record, $extraSubmit] = $result;
        }

        [$record, $operates, $format] = $this->handlePersistenceData($annotation, $record, $hooks, $output);

        if (!empty($this->input->submit)) {
            if ($this->entity) {
                return $this->persistence($record, $_annotation, $extraSubmit);
            } else {
                if (empty($this->input->handler) || !is_callable($this->input->handler)) {
                    throw new Exception(
                        "Persistence handler should be configured and callable when entity not configured"
                    );
                }
                $message = call_user_func_array(
                    $this->input->handler,
                    [$this->input->submit, $_annotation, $extraSubmit]
                );
                Helper::objectInstanceOf($message, Message::class, 'Persistence custom handler');

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
        }

        $output->id = $this->input->id;
        $output->record = $record;
        $output->operates = $operates;
        $output->formatJson = $this->json($format);

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