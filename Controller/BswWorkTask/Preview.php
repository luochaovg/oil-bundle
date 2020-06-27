<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
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
        $teamValue = $team ? "{$team}-{$this->usr('usr_uid')}" : null;

        return [
            'userId' => false,
            'team'   => [
                'label'      => 'User id',
                'field'      => 'bwt.userId',
                'type'       => SelectTree::class,
                'typeArgs'   => ['treeData' => $this->getTeamMemberTree($team), 'expandAll' => true],
                'filter'     => TeamMember::class,
                'filterArgs' => ['alias' => $this->previewAlias],
                'value'      => $teamValue,
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
                'value'      => date('Y-W'),
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
        [$team, $leader] = $this->workTaskTeam();

        return [
            'trail'     => [
                'width' => 120,
                'align' => 'center',
                'sort'  => 3.9,
                'html'  => true,
            ],
            Abs::TR_ACT => ['width' => ($team && !$leader) ? 80 : 126],
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

        return [$args->filter, $args->condition];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        [$team, $leader] = $this->workTaskTeam();

        $operates[] = (new Button('New task', 'app_bsw_work_task_simple', 'a:bug'))
            ->setType(Button::THEME_BSW_WARNING)
            ->setClick('showIFrame')
            ->setArgs(
                [
                    'width'  => Abs::MEDIA_SM,
                    'height' => 399,
                    'title'  => $this->twigLang('New task'),
                ]
            );

        if (!$team || $leader) {
            $operates[] = new Button('New record', 'app_bsw_work_task_persistence', $this->cnf->icon_newly);
        }

        $operates[] = (new Button('Logout'))
            ->setRoute($this->cnf->route_logout)
            ->setIcon($this->cnf->icon_logout)
            ->setConfirm($this->messageLang('Are you sure'));

        return $operates;
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        [$team, $leader] = $this->workTaskTeam();
        $userTeam = $this->getUserById($args->item['userId'])->teamId;

        $operates[] = (new Button('Progress'))
            ->setType(Button::THEME_BSW_WARNING)
            ->setRoute('app_bsw_work_task_progress')
            ->setClick('showIFrame')
            ->setDisabled(
                ($args->item['userId'] !== $this->usr('usr_uid')) &&
                !($leader && ($team === $userTeam))
            )
            ->setArgs(
                [
                    'id'     => $args->item['id'],
                    'width'  => 500,
                    'height' => 374,
                    'title'  => false,
                ]
            );

        if (!$team || $leader) {
            $operates[] = (new Button('Weight'))
                ->setType(Button::THEME_DEFAULT)
                ->setRoute('app_bsw_work_task_weight')
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 243,
                        'title'  => false,
                    ]
                );

            $operates[] = (new Button('Edit record', 'app_bsw_work_task_persistence'))
                ->setArgs(['id' => $args->item['id']]);

            $operates[] = (new Button('Close', 'app_bsw_work_task_close'))
                ->setType(Button::THEME_DANGER)
                ->setDisabled(!in_array($args->item['state'], [3, 4]))
                ->setConfirm($this->messageLang('Are you sure'))
                ->setArgs(['id' => $args->item['id']]);
        }

        return $operates;
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
            ->setType(Button::THEME_DEFAULT)
            ->setSize(Button::SIZE_SMALL)
            ->setClick('showTrailDrawer')
            ->setArgs(['id' => $args->original['id']]);

        $args->hooked['trail'] = $this->getButtonHtml($button);
        $args->hooked['trailList'] = $this->listTaskTrail($args->original['id']);

        if (in_array($args->hooked['state'], [3, 4])) {
            $args->hooked['rowClsName'] = "task-status-done";
        } elseif ($args->hooked['state'] === 1 && $args->original['startTime'] <= time()) {
            $args->hooked['rowClsName'] = "task-status-overdue";
        } elseif ($args->hooked['state'] === 2 && $args->original['endTime'] <= time()) {
            $args->hooked['rowClsName'] = "task-status-overdue";
        }

        return $args->hooked;
    }

    /**
     * @return array
     */
    public function previewTabsLinks(): array
    {
        return $this->tabsLinks();
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

        return $this->showPreview(
            [
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