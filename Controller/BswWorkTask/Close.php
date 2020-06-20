<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

trait Close
{
    /**
     * @return string
     */
    public function closeEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function closeAnnotationOnly(): array
    {
        return [
            'id'    => true,
            'state' => true,
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function closeAfterPersistence(Arguments $args)
    {
        return $this->trailLogger($args, $this->messageLang('Close the task'));
    }

    /**
     * Close task
     *
     * @Route("/bsw-work-task/close/{id}", name="app_bsw_work_task_close", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function close(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(
            [
                'id'     => $id,
                'submit' => ['id' => $id, 'state' => 0],
            ]
        );
    }
}