<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Date;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Input;
use Leon\BswBundle\Module\Form\Entity\Time;
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
        $args->submit['trail'] = $this->messageLang(
            '[{{ date }}] Create the task',
            ['{{ date }}' => date(Abs::FMT_FULL)]
        );

        return [$args->submit, $args->extraSubmit];
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
                'trail'       => false,
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
        $submit['userId'] = $this->usr->{$this->cnf->usr_uid};

        return [$submit, $extra];
    }

    /**
     * Simple persistence record
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
            'trail'  => ['show' => false],
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
     * @return array
     */
    public function weightAfterSubmit(Arguments $args)
    {
        $args->submit['trail'] = $args->recordBefore['trail'];
        $args->submit['trail'] .= PHP_EOL;
        $args->submit['trail'] .= $this->messageLang(
            '[{{ date }}] Change weight from {{ from }} to {{ to }}',
            [
                '{{ date }}' => date(Abs::FMT_FULL),
                '{{ from }}' => $args->recordBefore['weight'],
                '{{ to }}'   => $args->submit['weight'],
            ]
        );
        $args->submit['trail'] = trim($args->submit['trail']);

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * Adjustment weight
     *
     * @Route("/bsw-work-task/weight/{id}", name="app_bsw_work_task_weight", requirements={"id": "\d+"})
     * @Access(same="app_bsw_work_task_persistence")
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
            'trail'       => ['show' => false],
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
     * @return array
     */
    public function progressAfterSubmit(Arguments $args)
    {
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

        $args->submit['trail'] = $args->recordBefore['trail'];
        $args->submit['trail'] .= PHP_EOL;
        $args->submit['trail'] .= $this->messageLang(
            '[{{ date }}] Change progress from {{ from }} to {{ to }}{{ remark }}',
            [
                '{{ date }}'   => date(Abs::FMT_FULL),
                '{{ from }}'   => $args->recordBefore['donePercent'],
                '{{ to }}'     => $args->submit['donePercent'],
                '{{ remark }}' => rtrim(", {$args->extraSubmit['whatToDo']}", ', '),
            ]
        );
        $args->submit['trail'] = trim($args->submit['trail']);

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * Adjustment progress
     *
     * @Route("/bsw-work-task/progress/{id}", name="app_bsw_work_task_progress", requirements={"id": "\d+"})
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
}