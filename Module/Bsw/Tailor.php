<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Error\Error;

abstract class Tailor
{
    /**
     * @var BswWebController
     */
    protected $web;

    /**
     * @var array
     */
    protected $field;

    /**
     * @var string
     */
    protected $fieldUnder;

    /**
     * @var string
     */
    protected $fieldCamel;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $keyword;

    /**
     * Tailor constructor
     *
     * @param BswWebController $web
     * @param mixed            $field
     */
    public function __construct(BswWebController $web, $field)
    {
        $this->web = $web;
        $this->field = $field;

        if (is_string($this->field)) {
            $this->fieldHandler($this->field);
        }

        $this->initial();
    }

    /**
     * @param string $field
     */
    private function fieldHandler(string $field)
    {
        $this->fieldUnder = Helper::camelToUnder($field);
        $this->fieldCamel = Helper::underToCamel($this->fieldUnder);

        $this->label = Helper::stringToLabel($this->fieldUnder);
        $this->keyword = current(explode('_', $this->fieldUnder));
    }

    /**
     * @return mixed
     */
    protected function initial()
    {
        return null;
    }

    //
    // == Preview ==
    //

    /**
     * @param array $query
     *
     * @return array
     */
    public function tailorPreviewQuery(array $query): array
    {
        return $query;
    }

    /**
     * @param array $annotationExtra
     * @param array $annotation
     *
     * @return array
     */
    public function tailorPreviewAnnotation(array $annotationExtra, array $annotation): array
    {
        return $annotationExtra;
    }

    /**
     * @param array $original
     * @param array $extraArgs
     *
     * @return array
     */
    public function tailorPreviewBeforeHook(array $original, array $extraArgs): array
    {
        return $original;
    }

    /**
     * @param array $hooked
     * @param array $original
     * @param array $extraArgs
     *
     * @return array
     */
    public function tailorPreviewAfterHook(array $hooked, array $original, array $extraArgs): array
    {
        return $hooked;
    }

    /**
     * @param array $current
     * @param array $hooked
     * @param array $original
     *
     * @return array
     */
    public function tailorPreviewBeforeRender(array $current, array $hooked, array $original): array
    {
        return $current;
    }

    /**
     * @param Choice $choice
     *
     * @return Choice
     */
    public function tailorPreviewChoice(Choice $choice): Choice
    {
        return $choice;
    }

    //
    // == Persistence ==
    //

    /**
     * @param array $annotationExtra
     * @param array $annotation
     * @param int   $id
     *
     * @return array
     */
    public function tailorPersistenceAnnotation(array $annotationExtra, array $annotation, int $id): array
    {
        return $annotationExtra;
    }

    /**
     * @param array $submitItems
     * @param int   $id
     *
     * @return Error|array
     */
    public function tailorPersistenceAfterSubmit(array $submitItems, int $id)
    {
        return $submitItems;
    }

    /**
     * @param array $current
     * @param array $hooked
     * @param array $original
     * @param bool  $submit
     *
     * @return array
     */
    public function tailorPersistenceBeforeRender(array $current, array $hooked, array $original, bool $submit): array
    {
        return $current;
    }
}