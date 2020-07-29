<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Date;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Time;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Arguments $args
     *
     * @return array
     */
    public function persistenceAnnotation(Arguments $args): array
    {
        if ($args->id && !$args->persistence) {
            [$dateStart, $timeStart] = explode(' ', date(Abs::FMT_FULL, $args->record['startTime']));
            [$dateEnd, $timeEnd] = explode(' ', date(Abs::FMT_FULL, $args->record['endTime']));
        }

        if (!isset($timeStart) || !isset($timeEnd)) {
            [$hour, $minute] = explode(' ', date('H i', strtotime('+20 minutes')));
            $minute = floor($minute / 10) * 10;
            $timeStart = "{$hour}:{$minute}:00";
            $timeEnd = '18:00:00';
        }

        return [
            'title'           => ['label' => 'Mission title'],
            'weight'          => ['typeArgs' => $this->weightTypeArgs()],
            'lifecycle_start' => [
                'type'     => Group::class,
                'sort'     => 3.1,
                'typeArgs' => [
                    'column' => [14, 10],
                    'member' => [
                        (new Date())
                            ->setKey('day')
                            ->setPlaceholder('Start date')
                            ->setValue($dateStart ?? null)
                            ->setFormRules([$this->formRuleRequired($this->messageLang('Select start date please'))]),
                        (new Time())
                            ->setKey('time')
                            ->setPlaceholder('Start time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setValue($timeStart)
                            ->setFormRules([$this->formRuleRequired($this->messageLang('Select start time please'))]),
                    ],
                ],
            ],
            'lifecycle_end'   => [
                'type'     => Group::class,
                'sort'     => 3.2,
                'typeArgs' => [
                    'column' => [14, 10],
                    'member' => [
                        (new Date())
                            ->setKey('day')
                            ->setPlaceholder('End date')
                            ->setValue($dateEnd ?? null)
                            ->setFormRules([$this->formRuleRequired($this->messageLang('Select end date please'))]),
                        (new Time())
                            ->setKey('time')
                            ->setPlaceholder('End time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setValue($timeEnd)
                            ->setFormRules([$this->formRuleRequired($this->messageLang('Select end time please'))]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function persistenceOperates()
    {
        return $this->operatesButton();
    }

    /**
     * @param Arguments $args
     *
     * @return Message|array
     */
    public function persistenceAfterSubmit(Arguments $args)
    {
        $extra = $args->extraSubmit;
        $startTime = strtotime("{$extra['lifecycle_start_day']} {$extra['lifecycle_start_time']}");
        $endTime = strtotime("{$extra['lifecycle_end_day']} {$extra['lifecycle_end_time']}");

        if ($startTime >= $endTime) {
            return new Message('Start datetime should lte end');
        }

        if ($args->submit['type'] == 1) {
            $days = ($endTime - $startTime) / Abs::TIME_DAY;
            if ($days > $this->cnf->work_lifecycle_max_day) {
                return (new Message())
                    ->setMessage('Time span less than {{ day }} days')
                    ->setArgs(['{{ day }}' => $this->cnf->work_lifecycle_max_day]);
            }
        } else {
            [$team, $leader, $leaderId] = $this->workTaskTeamAndLeader();
            $args->submit['userId'] = $leaderId;
            $args->submit['weight'] = 0;
        }

        $args->submit['startTime'] = date(Abs::FMT_FULL, $startTime);
        $args->submit['endTime'] = date(Abs::FMT_FULL, $endTime);

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function persistenceAfterPersistence(Arguments $args)
    {
        if (!$args->newly) {
            return true;
        }

        $userId = $args->record['userId'];
        if ($this->usr('usr_uid') != $userId) {
            $this->sendTelegramTips(
                false,
                $userId,
                '{{ member }} create task {{ task }} for you',
                ['{{ task }}' => $args->record['title']]
            );
        }

        return $this->trailLogger($args, $this->messageLang('Create the task'));
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