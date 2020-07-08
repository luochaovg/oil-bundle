<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Repository\BswWorkTaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property Expr $expr
 */
trait Transfer
{
    /**
     * @return string
     */
    public function transferEntity(): string
    {
        return BswWorkTask::class;
    }

    /**
     * @return array
     */
    public function transferAnnotationOnly(): array
    {
        return [
            'id'     => true,
            'userId' => true,
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function transferFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit
            ->setBlock(true)
            ->setIcon('b:icon-feng')
            ->setLabel('Transfer task');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function transferAfterPersistence(Arguments $args)
    {
        /**
         * @var BswWorkTaskRepository $taskRepo
         */
        $taskRepo = $this->repo(BswWorkTask::class);
        $task = $taskRepo->find($args->original['id']);

        if ($this->usr('usr_uid') != $task->userId) {
            $this->sendTelegramTips(
                false,
                $task->userId,
                '{{ member }} transfer task {{ task }} to you',
                ['{{ task }}' => $task->title]
            );
        }

        $user = $this->getUserById($args->original['userId']);

        return $this->trailLogger(
            $args,
            $this->messageLang(
                'Transfer task to {{ to }}',
                ['{{ to }}' => $user->name]
            )
        );
    }

    /**
     * Transfer task
     *
     * @Route("/bsw-work-task/transfer/{id}", name="app_bsw_work_task_transfer", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function transfer(int $id = null): Response
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