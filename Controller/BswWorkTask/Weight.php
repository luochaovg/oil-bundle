<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Error\Entity\ErrorWithoutChange;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

trait Weight
{
    /**
     * @return string
     */
    public function weightEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function weightAnnotationOnly(): array
    {
        return [
            'id'     => true,
            'weight' => [
                'label'    => Helper::cnSpace(),
                'typeArgs' => $this->weightTypeArgs(),
                'style'    => ['margin-bottom' => '48px'],
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function weightFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit
            ->setBlock(true)
            ->setLabel('Update task weight');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function weightAfterSubmit(Arguments $args)
    {
        if ($args->recordBefore['weight'] == $args->submit['weight']) {
            return new ErrorWithoutChange();
        }

        return true;
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function weightAfterPersistence(Arguments $args)
    {
        return $this->trailLogger(
            $args,
            $this->messageLang(
                'Change weight from {{ from }} to {{ to }}',
                [
                    '{{ from }}' => $args->recordBefore['weight'],
                    '{{ to }}'   => $args->record['weight'],
                ]
            )
        );
    }

    /**
     * Adjustment task weight
     *
     * @Route("/bsw-work-task/weight/{id}", name="app_bsw_work_task_weight", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function weight(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }
}