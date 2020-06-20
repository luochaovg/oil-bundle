<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Controller\Traits\WorkTask;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * Bsw work task
 */
class Acme extends BswBackendController
{
    use WorkTask;
    use Preview;
    use Persistence;
    use PersistenceSimple;
    use Weight;
    use Progress;
    use Close;

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
}