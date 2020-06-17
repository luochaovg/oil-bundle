<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorMetaData;
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
    public function persistenceAnnotation(): array
    {
        return [
            'lifecycle' => [
                'label'    => '生命周期',
                'type'     => Group::class,
                'sort'     => 3,
                'typeArgs' => [
                    'column' => [6, 5, 2, 6, 5],
                    'member' => [
                        (new Date())
                            ->setField('start_day')
                            ->setPlaceholder('Start day')
                            ->setRules([$this->formRuleRequired($this->messageLang('Select start date please'))]),
                        (new Time())
                            ->setField('start_time')
                            ->setPlaceholder('Start time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select start time please'))]),
                        (new Text())
                            ->setStyle(['width' => '100%', 'text-align' => 'center'])
                            ->setValue('~'),
                        (new Date())
                            ->setField('end_day')
                            ->setPlaceholder('End time')
                            ->setRules([$this->formRuleRequired($this->messageLang('Select end date please'))]),
                        (new Time())
                            ->setField('end_time')
                            ->setPlaceholder('End time')
                            ->setMinuteStep(10)
                            ->setSecondStep(60)
                            ->setRules([$this->formRuleRequired($this->messageLang('Select end time please'))]),
                    ],
                ],
            ],
        ];
    }

    /**
     * Cal hours in work day
     *
     * @return float|Error
     */
    protected function workDayHours()
    {
        $keys = [
            'work_morning_start',
            'work_noon_break_start',
            'work_noon_break_end',
            'work_night_end',
        ];

        foreach ($keys as $key) {
            if (!Helper::isDateString($this->cnf->{$key})) {
                return new ErrorMetaData();
            }
        }

        $firstPart = Helper::gapDateTime($this->cnf->work_morning_start, $this->cnf->work_noon_break_start);
        $secondPart = Helper::gapDateTime($this->cnf->work_noon_break_end, $this->cnf->work_night_end);

        return ($firstPart + $secondPart) / Abs::TIME_HOUR;
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
            return new Message('Start datetime should lte end', Abs::TAG_CLASSIFY_ERROR);
        }

        $hours = $this->workDayHours();
        if (!is_numeric($hours)) {
            return $hours;
        }

        $args->submit['userId'] = $this->usr->{$this->cnf->usr_uid};
        $args->submit['startTime'] = $startTime;
        $args->submit['endTime'] = $endTime;

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
}