<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Carbon\Carbon;
use Doctrine\ORM\AbstractQuery;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Filter\Entity\TeamMember;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Leon\BswBundle\Repository\BswWorkTaskTrailRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bsw work task
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
    use PersistenceSimple;
    use Weight;
    use Progress;
    use Close;
    use WeekReport;

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
     * Trail logger
     *
     * @param Arguments $args
     * @param string    $trail
     * @param bool      $reliable
     *
     * @return bool|Error
     * @throws
     */
    protected function trailLogger(Arguments $args, string $trail, bool $reliable = false)
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $result = $trailRepo->newly(
            [
                'userId'   => $this->usr('usr_uid'),
                'taskId'   => intval($args->newly ? $args->result : $args->original['id']),
                'reliable' => $reliable ? 1 : 0,
                'trail'    => $trail,
            ]
        );

        if ($result === false) {
            return new ErrorDbPersistence();
        }

        return true;
    }

    /**
     * Get work task team info
     *
     * @return array
     */
    protected function workTaskTeam(): array
    {
        return [$this->usr('usr_team'), $this->usr('usr_team_leader')];
    }

    /**
     * Get admin by id
     *
     * @param int $userId
     *
     * @return mixed
     * @throws
     */
    protected function getUserById(int $userId)
    {
        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);

        return $adminRepo->find($userId);
    }

    /**
     * List task trail
     *
     * @param int $taskId
     *
     * @return array
     * @throws
     */
    protected function listTaskTrail(int $taskId): array
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $list = $trailRepo->lister(
            [
                'limit'  => 0,
                'alias'  => 't',
                'select' => ['u.name', 't.reliable', 't.trail', 't.addTime AS time'],
                'join'   => [
                    'u' => [
                        'entity' => BswAdminUser::class,
                        'left'   => ['t.userId'],
                        'right'  => ['u.id'],
                    ],
                ],
                'where'  => [
                    $this->expr->eq('t.taskId', ':task'),
                    $this->expr->eq('t.state', ':state'),
                ],
                'args'   => [
                    'task'  => [$taskId],
                    'state' => [Abs::NORMAL],
                ],
                'order'  => ['t.id' => Abs::SORT_ASC],
            ]
        );

        return $this->taskTrailHandler($list);
    }

    /**
     * Task trail handler
     *
     * @param array $list
     *
     * @return array
     */
    protected function taskTrailHandler(array $list): array
    {
        $lang = $this->langLatest(['cn' => 'zh-CN', 'en' => 'en'], 'en');
        foreach ($list as &$item) {
            $cb = Carbon::createFromFormat(Abs::FMT_FULL, $item['time']);
            $item['human'] = $cb->locale($lang)->diffForHumans();
            $item['color'] = Helper::colorValue($item['name'], true);
            $item['name'] = current(explode(' ', $item['name']));
            $item['time'] = date('m/d H:i', strtotime($item['time']));
        }

        return $list;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraUserId(Arguments $args): array
    {
        [$team] = $this->workTaskTeam();

        if ($team) {
            $filter = [
                'where' => [$this->expr->eq('kvp.teamId', ':team')],
                'args'  => ['team' => [$team]],
            ];
        } else {
            $filter = [
                'where' => [$this->expr->gt('kvp.teamId', ':team')],
                'args'  => ['team' => [0]],
            ];
        }

        $teamFilter = [
            'join' => [
                'bwt' => [
                    'entity' => BswWorkTeam::class,
                    'left'   => ['kvp.teamId'],
                    'right'  => ['bwt.id'],
                ],
            ],
        ];

        return $this->repo(BswAdminUser::class)->kvp(
            ['kvp.name', 'bwt.name AS team'],
            Abs::PK,
            function ($v) {
                return $v['name'];
                // return "[{$v['team']}] {$v['name']}";
            },
            $filter,
            $teamFilter
        );
    }

    /**
     * Get team member
     *
     * @param int $teamId
     *
     * @return array
     * @throws
     */
    protected function getTeamMemberMap(?int $teamId = null): array
    {
        $teamFilter = [];
        if ($teamId) {
            $teamFilter = [
                'where' => [$this->expr->eq('u.teamId', ':teamId')],
                'args'  => ['teamId' => [$teamId]],
            ];
        }

        /**
         * @var BswAdminUserRepository $userRepo
         */
        $userRepo = $this->repo(BswAdminUser::class);

        return $userRepo->filters($teamFilter)->lister(
            [
                'limit'  => 0,
                'alias'  => 'u',
                'select' => [
                    'u.teamId',
                    't.name AS teamName',
                    'u.id AS memberId',
                    'u.name AS memberName',
                ],
                'join'   => [
                    't' => [
                        'entity' => BswWorkTeam::class,
                        'left'   => ['u.teamId'],
                        'right'  => ['t.id'],
                    ],
                ],
                'where'  => [
                    $this->expr->gt('u.teamId', ':team'),
                    $this->expr->eq('u.state', ':state'),
                ],
                'args'   => [
                    'team'  => [0],
                    'state' => [Abs::NORMAL],
                ],
            ]
        );
    }

    /**
     * Get team member
     *
     * @param int $teamId
     *
     * @return array
     */
    protected function getTeamMemberTree(?int $teamId = null): array
    {
        static $tree;

        if (isset($tree)) {
            return $tree;
        }

        $tree = [];
        $teamMember = $this->getTeamMemberMap($teamId);

        foreach ($teamMember as $item) {
            $tt = $item['teamId'];
            $mm = $item['memberId'];
            if (!isset($tree[$tt])) {
                $tree[$tt] = [
                    'title'    => $item['teamName'],
                    'value'    => $tt,
                    'children' => [],
                ];
            }
            if (!empty($mm) && !isset($tree[$tt]['children'][$mm])) {
                $tree[$tt]['children'][$mm] = [
                    'title'    => $item['memberName'],
                    'value'    => "{$tt}-{$mm}",
                    'children' => [],
                ];
            }
        }

        $tree = Helper::arrayValues(
            $tree,
            function ($value) {
                return is_numeric(key($value));
            }
        );

        return $tree;
    }

    /**
     * Correct team member filter
     *
     * @param string $field
     * @param array  $alias
     * @param array  $condition
     *
     * @return array
     */
    protected function correctTeamMemberFilter(string $field, array $alias, ?array $condition = null): array
    {
        $condition = $condition ?? [];
        [$team] = $this->workTaskTeam();
        if (!$team) {
            return $condition;
        }

        $filter = $condition[$field]['filter'] ?? null;
        $value = $condition[$field]['value'] ?? null;

        if (!$filter) {
            $filter = (new TeamMember())->setAlias($alias);
        }

        $value = $filter->correctTeamMemberByAgency($team, $value);
        $condition[$field] = $this->createFilter($filter, $value);

        return $condition;
    }

    /**
     * @return array
     */
    protected function tabsLinks(): array
    {
        [$team, $leader] = $this->workTaskTeam();

        $links[] = new Links(
            $this->fieldLang('Task list'),
            'app_bsw_work_task_preview',
            'b:icon-mark'
        );

        $links[] = new Links(
            $this->fieldLang('Weekly publication'),
            'app_bsw_work_week_report',
            'b:icon-calendar'
        );

        $links[] = (new Links($this->fieldLang('Progress chart')))
            ->setRoute('app_bsw_work_week_survey')
            ->setIcon('a:line-chart')
            ->setClick('showModal')
            ->setArgs(
                [
                    'title'   => 'Oops',
                    'width'   => Abs::MEDIA_MIN,
                    'content' => $this->fieldLang('Look forward'),
                ]
            );

        return $links;
    }

    /**
     * Before action logic
     */
    public function beforeLogic()
    {
        [$team, $leader] = $this->workTaskTeam();
        if (!$team) {
            return null;
        }

        $title = $this->fieldLang('Work task manager');
        $this->seoTitle = $title;

        $leader = $leader ? ' 🚩' : null;
        $this->cnf->copyright = "{$title} © {$this->usr('usr_account')}{$leader}";
        $this->logic->display = ['menu', 'header'];
    }

    /**
     * @param array $args
     *
     * @return array|Error|object|Response
     * @throws
     */
    public function webShouldAuth(array $args)
    {
        $token = $this->getArgs('token');
        if ($token && $record = $this->checkSceneToken($token, 1)) {

            $this->session->clear();
            if ($record instanceof Error) {
                return $record;
            }

            $user = $this->repo(BswAdminUser::class)->lister(
                [
                    'limit' => 1,
                    'where' => [
                        $this->expr->eq('bau.telegramId', ':telegram'),
                        $this->expr->gt('bau.teamId', ':team'),
                    ],
                    'args'  => [
                        'telegram' => [$record->userId],
                        'team'     => [0],
                    ],
                ],
                AbstractQuery::HYDRATE_OBJECT
            );

            if ($user) {
                $this->loginAdminUser($user, $this->getClientIp());

                return $this->redirectToRoute($this->route);
            }
        }

        return parent::webShouldAuth($args);
    }
}