<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw work task
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
    public function acmeEnumExtraUserId(Arguments $args): array
    {
        $filter = [
            'where' => [$this->expr->eq('kvp.roleId', ':role')],
            'args'  => ['role' => [$this->cnf->work_role_id]],
        ];

        return $this->repo(BswAdminUser::class)->kvp(['name'], Abs::PK, null, $filter);
    }
}