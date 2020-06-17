<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Date;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Text;
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
            'title'     => ['label' => 'Mission title'],
            'weight'    => ['typeArgs' => $this->weightTypeArgs()],
            'lifecycle' => [
                'type'     => Group::class,
                'sort'     => 3,
                'typeArgs' => [
                    'column' => [6, 5, 2, 6, 5],
                    'member' => [
                        (new Date())
                            ->setField('start_day')
                            ->setPlaceholder('Start day')
                            ->setValue($dateStart ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select start date please'))]),
                        (new Time())
                            ->setField('start_time')
                            ->setPlaceholder('Start time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setValue($timeStart ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select start time please'))]),
                        (new Text())
                            ->setStyle(['width' => '100%', 'text-align' => 'center'])
                            ->setValue('~'),
                        (new Date())
                            ->setField('end_day')
                            ->setPlaceholder('End time')
                            ->setValue($dateEnd ?? null)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select end date please'))]),
                        (new Time())
                            ->setField('end_time')
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
     * @return Message|Error|array
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
    public function weightEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function weightFormOperates(Arguments $args)
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setBlock();

        return ['submit' => $submit];
    }

    /**
     * @return array
     */
    public function weightAnnotationOnly(): array
    {
        return [
            'id'     => true,
            'weight' => [
                'label'    => false,
                'typeArgs' => $this->weightTypeArgs(),
            ],
        ];
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
        return $this->persistence($id);
    }

    /**
     * @return string
     */
    public function progressEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function progressFormOperates(Arguments $args)
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setBlock();

        return ['submit' => $submit];
    }

    /**
     * @return array
     */
    public function progressAnnotationOnly(): array
    {
        return [
            'id'          => true,
            'donePercent' => ['label' => false],
        ];
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
        return $this->persistence($id);
    }
}