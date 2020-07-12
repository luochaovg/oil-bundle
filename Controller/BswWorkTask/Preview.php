<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Entity\Accurate;
use Leon\BswBundle\Module\Filter\Entity\Senior;
use Leon\BswBundle\Module\Filter\Entity\TeamMember;
use Leon\BswBundle\Module\Filter\Entity\WeekIntersect;
use Leon\BswBundle\Module\Form\Entity\SelectTree;
use Leon\BswBundle\Module\Form\Entity\Week;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property Expr $expr
 */
trait Preview
{
    /**
     * @var array
     */
    protected $previewAlias = [
        'team'   => ['bau', 'teamId'],
        'member' => ['bwt', 'userId'],
    ];

    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswWorkTask::class;
    }

    /**
     * @return array
     */
    public function previewFilterAnnotation(): array
    {
        [$team] = $this->workTaskTeam();

        return [
            'userId' => false,
            'team'   => $this->isTeamTask ? false : [
                'label'      => 'User id',
                'field'      => 'bwt.userId',
                'type'       => SelectTree::class,
                'typeArgs'   => ['treeData' => $this->getTeamMemberTree($team), 'expandAll' => true],
                'filter'     => TeamMember::class,
                'filterArgs' => ['alias' => $this->previewAlias],
                'value'      => $this->teamDefaultValue(),
                'column'     => 3,
                'sort'       => 1,
            ],
            'week'   => [
                'label'      => 'Week n',
                'field'      => 'bwt.addTime',
                'type'       => Week::class,
                'filter'     => WeekIntersect::class,
                'filterArgs' => [
                    'timestamp' => true,
                    'carryTime' => false,
                    'alias'     => ['from' => 'bwt.startTime', 'to' => 'bwt.endTime'],
                ],
                'sort'       => 3,
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewQuery(): array
    {
        return [
            'select' => ['bwt'],
            'join'   => [
                'bau' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['bwt.userId'],
                    'right'  => ['bau.id'],
                ],
            ],
            'sort'   => ['bwt.id' => Abs::SORT_DESC],
        ];
    }

    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'userId'    => !$this->isTeamTask,
            'weight'    => !$this->isTeamTask,
            'trail'     => [
                'width' => 120,
                'align' => Abs::POS_CENTER,
                'sort'  => 3.9,
                'html'  => true,
            ],
            Abs::TR_ACT => [
                'width' => 156,
                'align' => Abs::POS_LEFT,
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewFilterCorrect(Arguments $args): array
    {
        if (empty($args->condition['bwt.state'])) {
            $args->condition['bwt.state'] = $this->createFilter(Senior::class, [Senior::GT, [0]]);
        }

        $args->condition = $this->correctTeamMemberFilter(
            'bwt.userId',
            $this->previewAlias,
            $args->condition ?? null
        );

        $args->condition['bwt.type'] = $this->createFilter(Accurate::class, $this->isTeamTask ? 2 : 1);

        return [$args->filter, $args->condition];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return $this->operatesButton();
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        [$team, $leader] = $this->workTaskTeam();

        $isMyTask = $args->item['userId'] === $this->usr('usr_uid');
        $isMyTeam = $team === $this->getUserById($args->item['userId'])->teamId;

        return [
            (new Button('Progress'))
                ->setType(Abs::THEME_BSW_SUCCESS)
                ->setRoute('app_bsw_work_task_progress')
                ->setIcon('b:icon-process')
                ->setClick('showIFrame')
                ->setDisabled(!($isMyTask || ($isMyTeam && $leader)))
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 426,
                        'title'  => false,
                    ]
                ),

            (new Button('Edit record'))
                ->setRoute('app_bsw_work_task_persistence')
                ->setIcon('b:icon-edit')
                ->setDisplay($leader)
                ->setArgs(['id' => $args->item['id']]),

            (new Button('Notes'))
                ->setRoute('app_bsw_work_task_notes')
                ->setIcon('b:icon-form')
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'fill'   => ['taskId' => $args->item['id']],
                        'type'   => $this->isTeamTask ? 'team' : 'member',
                        'width'  => $this->isTeamTask ? Abs::MEDIA_SM : 500,
                        'height' => $this->isTeamTask ? 433 : 313,
                        'title'  => false,
                    ]
                ),

            (new Button('Transfer'))
                ->setRoute('app_bsw_work_task_transfer')
                ->setIcon('b:icon-feng')
                ->setDisplay($leader && !$this->isTeamTask)
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => Abs::MEDIA_MIN,
                        'height' => 220,
                        'title'  => false,
                    ]
                ),

            (new Button('Weight'))
                ->setType(Abs::THEME_DEFAULT)
                ->setRoute('app_bsw_work_task_weight')
                ->setIcon('b:icon-jewelry')
                ->setDisplay($leader && !$this->isTeamTask)
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 253,
                        'title'  => false,
                    ]
                ),

            (new Button('Close'))
                ->setType(Abs::THEME_DANGER)
                ->setRoute('app_bsw_work_task_close')
                ->setIcon('b:icon-success')
                ->setDisplay($leader)
                ->setDisabled(!in_array($args->item['state'], [3, 4]))
                ->setConfirm($this->messageLang('Are you sure'))
                ->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * @param Arguments $args
     * @param string    $left
     * @param string    $right
     *
     * @return Charm
     */
    public function previewCharmStartTime(Arguments $args, string $left = 'Ready', string $right = 'Consumed')
    {
        if (!in_array($args->item['state'], [1, 2])) {
            return new Charm(Abs::HTML_CODE, $args->value);
        }

        $left = $this->fieldLang($left);
        $right = $this->fieldLang($right);
        $html = Abs::HTML_CODE . Abs::LINE_DASHED;

        [$gap, $tip] = Helper::gapDateDetail(
            $args->value,
            [
                'year'   => $this->fieldLang('Year'),
                'month'  => $this->fieldLang('Month'),
                'day'    => $this->fieldLang('Day'),
                'hour'   => $this->fieldLang('Hour'),
                'minute' => $this->fieldLang('Minute'),
                'second' => $this->fieldLang('Second'),
            ]
        );

        if ($gap >= 0) {
            $html .= str_replace('{value}', "{$left}: {$tip}", Abs::HTML_GREEN_TEXT);
        } else {
            $html .= str_replace('{value}', "{$right}: {$tip}", Abs::HTML_ORANGE_TEXT);
        }

        return new Charm($html, $args->value);
    }

    /**
     * @param Arguments $args
     *
     * @return Charm
     */
    public function previewCharmEndTime(Arguments $args)
    {
        return $this->previewCharmStartTime($args, 'Surplus', 'Expired');
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        $button = (new Button('lifecycle'))
            ->setType(Abs::THEME_DEFAULT)
            ->setSize(Abs::SIZE_SMALL)
            ->setClick('showTrailDrawer')
            ->setArgs(['id' => $args->original['id']]);

        $args->hooked['trail'] = $this->getButtonHtml($button);
        $args->hooked['trailList'] = $this->listTaskTrail($args->original['id']);

        if (in_array($args->hooked['state'], [3, 4])) {
            $args->hooked[Abs::TAG_ROW_CLS_NAME] = 'task-status-done';
        } elseif ($args->hooked['state'] === 1 && $args->original['startTime'] <= time()) {
            $args->hooked[Abs::TAG_ROW_CLS_NAME] = 'task-status-overdue';
        } elseif ($args->hooked['state'] === 2 && $args->original['endTime'] <= time()) {
            $args->hooked[Abs::TAG_ROW_CLS_NAME] = 'task-status-overdue';
        }

        return $args->hooked;
    }

    /**
     * Preview record
     *
     * @Route("/bsw-work-task/preview", name="app_bsw_work_task_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        [$team] = $this->workTaskTeam();

        return $this->showPreview(
            [
                'border'      => !$team,
                'dynamic'     => 10,
                'filterJump'  => true,
                'pageJump'    => true,
                'afterModule' => [
                    'drawer' => function ($logic, $args) {
                        $trailVisible = array_column($args['preview']['list'], 'id');
                        $trailVisible = Helper::arrayValuesSetTo($trailVisible, false, true);
                        $trailVisible = Helper::jsonStringify($trailVisible);

                        return compact('trailVisible');
                    },
                ],
            ],
            [],
            'task/preview.html'
        );
    }
}