<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Monolog\Logger;

trait WorkTask
{
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
        $this->logic->display = ['menu', 'header', 'crumbs'];
    }
}