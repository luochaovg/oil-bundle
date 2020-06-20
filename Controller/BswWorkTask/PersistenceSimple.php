<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

trait PersistenceSimple
{
    /**
     * @return string
     */
    public function simpleEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function simpleAnnotation(Arguments $args): array
    {
        $annotation = $this->persistenceAnnotation($args);
        $annotation = array_merge(
            $annotation,
            [
                'userId'      => false,
                'donePercent' => false,
                'weight'      => false,
                'remark'      => false,
                'state'       => false,
            ]
        );

        return $annotation;
    }

    /**
     * @param Arguments $args
     *
     * @return Message|array
     */
    public function simpleAfterSubmit(Arguments $args)
    {
        $result = $this->persistenceAfterSubmit($args);
        if (!is_array($result)) {
            return $result;
        }

        [$submit, $extra] = $result;
        $submit['userId'] = $this->usr('usr_uid');

        return [$submit, $extra];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function simpleAfterPersistence(Arguments $args)
    {
        return $this->trailLogger($args, $this->messageLang('Create the task'));
    }

    /**
     * Add task
     *
     * @Route("/bsw-work-task/simple/{id}", name="app_bsw_work_task_simple", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function simple(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id, 'nextRoute' => 'app_bsw_work_task_preview']);
    }
}