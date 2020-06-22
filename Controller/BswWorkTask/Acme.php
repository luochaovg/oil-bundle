<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Carbon\Carbon;
use Doctrine\ORM\AbstractQuery;
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
     *
     * @return bool|Error
     * @throws
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
     * @return array
     */
    protected function tabsLinks(): array
    {
        return [
            (new Links($this->fieldLang('Task list'), 'app_bsw_work_task_preview', 'b:icon-mark')),
            (new Links($this->fieldLang('Progress chart'), 'app_bsw_work_week_survey', 'a:line-chart'))
                ->setClick('showModal')
                ->setArgs(
                    [
                        'title'   => 'Oops',
                        'width'   => Abs::MEDIA_MIN,
                        'content' => $this->fieldLang('Look forward'),
                    ]
                ),
            (new Links($this->fieldLang('Weekly publication'), 'app_bsw_work_week_report', 'b:icon-calendar'))
                ->setClick('showModal')
                ->setArgs(
                    [
                        'title'   => 'Oops',
                        'width'   => Abs::MEDIA_MIN,
                        'content' => $this->fieldLang('Look forward'),
                    ]
                ),
        ];
    }

    /**
     * Trail list to string
     *
     * @param array $list
     *
     * @return string
     */
    protected function trailListStringify(array $list): ?string
    {
        $trail = null;
        $lang = $this->langLatest(['cn' => 'zh-CN', 'en' => 'en'], 'en');

        foreach ($list as $item) {
            $cb = Carbon::createFromFormat(Abs::FMT_FULL, $item['time']);
            $cb = $cb->locale($lang)->diffForHumans();
            $cb = Html::tag('span', "({$cb})", ['style' => ['color' => '#ccc', 'font-size' => '12px']]);

            $trail .= str_replace('{value}', $item['time'], Abs::HTML_CODE) . ' ';
            $trail .= str_replace('{value}', $item['name'], Abs::TEXT_BLUE) . ' ';
            $trail .= $item['trail'] . ' ';
            $trail .= $cb;
            $trail .= str_replace(10, 15, Abs::LINE_DASHED);
        }

        return $trail;
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
                return "[{$v['team']}] {$v['name']}";
            },
            $filter,
            $teamFilter
        );
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

        $leader = $leader ? ' 🚩' : null;
        $this->cnf->copyright = "working task manager © {$this->usr('usr_account')}{$leader}";
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