<?php

namespace Leon\BswBundle\Controller\Traits;

use Carbon\Carbon;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Leon\BswBundle\Repository\BswWorkTaskTrailRepository;

trait WorkTask
{
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
    public function workTaskTeam(): array
    {
        return [$this->usr('usr_team'), $this->usr('usr_team_leader')];
    }

    /**
     * Get work task team by user id
     *
     * @param int $userId
     *
     * @return mixed
     * @throws
     */
    public function workTaskTeamByUserId(int $userId)
    {
        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);
        $admin = $adminRepo->find($userId);

        return $admin->teamId;
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
     * @return array
     */
    public function tabsLinks(): array
    {
        return [
            (new Links('任务列表', 'app_bsw_work_task_preview', 'b:icon-mark')),
            (new Links('进展图', 'app_bsw_work_week_survey', 'a:line-chart')),
            (new Links('周报', 'app_bsw_work_week_report', 'b:icon-calendar')),
        ];
    }

    /**
     * Trail list to string
     *
     * @param array $list
     *
     * @return string
     */
    public function trailListStringify(array $list): string
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
}