<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAccess;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Date;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Input;
use Leon\BswBundle\Module\Form\Entity\Time;
use Leon\BswBundle\Repository\BswWorkTaskTrailRepository;
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
    protected function weightTypeArgs(): array
    {
        $weekendDays = floor($this->cnf->work_lifecycle_max_day / 7) * 2;
        $workDays = $this->cnf->work_lifecycle_max_day - $weekendDays;
        $maxHours = ceil($workDays * $this->cnf->work_lifecycle_day_hours);

        return ['min' => 1, 'max' => $maxHours];
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
                            ->setField('day')
                            ->setPlaceholder('Start date')
                            ->setValue($dateStart ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select start date please'))]),
                        (new Time())
                            ->setField('time')
                            ->setPlaceholder('Start time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setValue($timeStart ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select start time please'))]),
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
                            ->setField('day')
                            ->setPlaceholder('End date')
                            ->setValue($dateEnd ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select end date please'))]),
                        (new Time())
                            ->setField('time')
                            ->setPlaceholder('End time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setValue($timeEnd ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select end time please'))]),
                    ],
                ],
            ],
        ];
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

        $days = ($endTime - $startTime) / Abs::TIME_DAY;
        if ($days > $this->cnf->work_lifecycle_max_day) {
            return (new Message())
                ->setMessage('Time span less than {{ day }} days')
                ->setArgs(['{{ day }}' => $this->cnf->work_lifecycle_max_day]);
        }

        $args->submit['startTime'] = date(Abs::FMT_FULL, $startTime);
        $args->submit['endTime'] = date(Abs::FMT_FULL, $endTime);

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * Trail logger
     *
     * @param Arguments $args
     * @param string    $trail
     *
     * @return bool|Error
     */
    protected function trailLogger(Arguments $args, string $trail)
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $result = $trailRepo->newly(
            [
                'userId' => $this->usr('usr_uid'),
                'taskId' => intval($args->newly ? $args->result : $args->original['id']),
                'trail'  => $trail,
            ]
        );

        if ($result === false) {
            return new ErrorDbPersistence();
        }

        return true;
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function persistenceAfterPersistence(Arguments $args)
    {
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
            'whatToDo'    => ['type' => Input::class, 'rules' => [$this->formRuleRequired()]],
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
        [$myTeam, $leader] = $this->workTaskTeam();
        $userTeam = $this->workTaskTeamByUserId($args->recordBefore['userId']);

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
            )
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

        return $this->showPersistence(['id' => $id]);
    }

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