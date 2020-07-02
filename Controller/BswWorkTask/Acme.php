<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Carbon\Carbon;
use Doctrine\ORM\AbstractQuery;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
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
                'trail'    => Html::cleanHtml($trail),
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
     * Get team default value
     *
     * @return string
     */
    public function teamDefaultValue(): ?string
    {
        [$team, $leader] = $this->workTaskTeam();
        if (!$team) {
            return null;
        }

        if ($leader) {
            return "{$team}";
        }

        return "{$team}-{$this->usr('usr_uid')}";
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
                'select' => ['t.id', 'u.name', 't.reliable', 't.trail', 't.addTime AS time'],
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
     * @throws
     */
    protected function taskTrailHandler(array $list): array
    {
        $lang = $this->langLatest(['cn' => 'zh-CN', 'en' => 'en'], 'en');
        foreach ($list as &$item) {

            $cb = Carbon::createFromFormat(Abs::FMT_FULL, $item['time']);
            $item['human'] = $cb->locale($lang)->diffForHumans();
            [$item['name'], $item['color']] = $this->nameToColor($item['name']);
            $item['time'] = date('m/d H:i', strtotime($item['time']));

            // mentions
            $member = $this->matchMentions(Html::cleanHtml($item['trail']));
            foreach ($member as $v) {
                $name = Html::tag('a', "@{$v['name']}", ['href' => 'javascript:;']);
                $item['trail'] = str_replace($v['block'], $name, $item['trail']);
            }

            // links
            preg_match_all('/https?\:\/\/[\S]+/i', $item['trail'], $result);
            foreach ($result[0] ?? [] as $link) {
                $linkHtml = Html::tag('a', $link, ['href' => $link, 'target' => '_blank']);
                $item['trail'] = str_replace($link, $linkHtml, $item['trail']);
            }
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
     * Send telegram tips
     *
     * @param bool   $isTelegramId
     * @param int    $id // User id or Telegram id
     * @param string $messageLabel
     * @param array  $messageArgs
     * @param string $route
     */
    public function sendTelegramTips(
        bool $isTelegramId,
        int $id,
        string $messageLabel,
        array $messageArgs = [],
        string $route = 'app_bsw_work_task_preview'
    ) {
        if (!$this->cnf->work_task_send_telegram) {
            return;
        }

        if ($isTelegramId) {
            $telegramId = $id;
        } else {
            $telegramId = $this->getUserById($id)->telegramId;
        }

        if (!$telegramId) {
            return;
        }

        $url = $this->url($route, ['token' => $this->createSceneToken(1, $telegramId)]);
        $url = "[Doorway]({$url})";

        $member = $this->usr('usr_account');
        $message = $this->messageLang(
            $messageLabel,
            array_merge(['{{ member }}' => $member], $messageArgs)
        );

        $this->telegramSendMessage($telegramId, "\[{$url}] {$message}");
    }

    /**
     * Match mentions
     *
     * @param string $content
     *
     * @return array
     */
    protected function matchMentions(string $content): array
    {
        $member = [];
        preg_match_all('/@(.*?)\!(\d+)/', $content, $result);
        foreach ($result[0] ?? [] as $key => $block) {
            $member[] = [
                'block'      => $block,
                'telegramId' => $result[2][$key],
                'name'       => $result[1][$key],
            ];
        }

        return $member;
    }

    /**
     * @return array
     */
    protected function tabsLinks(): array
    {
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