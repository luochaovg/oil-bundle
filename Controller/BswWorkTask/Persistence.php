<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Number;
use Leon\BswBundle\Module\Form\Entity\Select;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswWorkTask::class;
    }

    /**
     * @return array
     */
    public function persistenceAnnotation(): array
    {
        return [
            'lifecycle' => [
                'label'    => '生命周期',
                'type'     => Group::class,
                'sort'     => 3,
                'typeArgs' => [
                    'member' => [
                        (new Datetime())
                            ->setField('start')
                            ->setPlaceholder('Start time')
                            ->setRules([$this->formRuleRequired('请选择开始时间')]),
                        (new Datetime())
                            ->setField('end')
                            ->setPlaceholder('End time')
                            ->setRules([$this->formRuleRequired('请选择结束时间')]),
                    ],
                ],
            ],
        ];
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-work-task/persistence/{id}", name="app_bsw_work_task_persistence", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function persistence(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }
}