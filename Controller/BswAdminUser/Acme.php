<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw admin user
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraRoleId(Arguments $args): array
    {
        $role = $this->repo(BswAdminRole::class)->kvp(['name']);
        $role = [0 => Abs::UNALLOCATED] + $role;

        return $role;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraTeamId(Arguments $args): array
    {
        $team = $this->repo(BswWorkTeam::class)->kvp(['name']);
        $team = [0 => Abs::UNALLOCATED] + $team;

        return $team;
    }
}