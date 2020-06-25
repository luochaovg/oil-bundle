<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Error\Entity\ErrorAccess;
use Leon\BswBundle\Module\Error\Entity\ErrorProgress;
use Leon\BswBundle\Module\Error\Entity\ErrorWithoutChange;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\TextArea;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

trait Progress
{
    /**
     * @return string
     */
    public function progressEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function progressAnnotationOnly(): array
    {
        return [
            'id'          => true,
            'donePercent' => ['label' => Helper::cnSpace()],
            'whatToDo'    => [
                'type'     => TextArea::class,
                'typeArgs' => ['maxRows' => 4],
                'rules'    => [$this->formRuleRequired()],
            ],
            'state'       => ['show' => false],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function progressFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit
            ->setBlock(true)
            ->setLabel('Update task progress');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return Error|array
     */
    public function progressAfterSubmit(Arguments $args)
    {
        if ($args->recordBefore['donePercent'] >= $args->submit['donePercent']) {
            return new ErrorProgress();
        }

        [$myTeam, $leader] = $this->workTaskTeam();
        $userTeam = $this->getUserById($args->recordBefore['userId'])->teamId;

        if (
            ($args->recordBefore['userId'] !== $this->usr('usr_uid')) &&
            !($leader && ($myTeam === $userTeam))
        ) {
            return new ErrorAccess();
        }

        if ($args->submit['donePercent'] <= 0) {
            $args->submit['state'] = 1;
        } elseif ($args->submit['donePercent'] < 100) {
            $args->submit['state'] = 2;
        } else {
            if (time() <= $args->recordBefore['endTime']) {
                $args->submit['state'] = 3;
            } else {
                $args->submit['state'] = 4;
            }
        }

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function progressAfterPersistence(Arguments $args)
    {
        return $this->trailLogger(
            $args,
            $this->messageLang(
                'Change progress from {{ from }} to {{ to }}{{ remark }}',
                [
                    '{{ from }}'   => $args->recordBefore['donePercent'],
                    '{{ to }}'     => $args->record['donePercent'],
                    '{{ remark }}' => $args->extraSubmit['whatToDo'],
                ]
            ),
            true
        );
    }

    /**
     * Adjustment task progress
     *
     * @Route("/bsw-work-task/progress/{id}", name="app_bsw_work_task_progress", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function progress(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(
            [
                'id'   => $id,
                'sets' => ['function' => 'refreshPreview'],
            ]
        );
    }
}