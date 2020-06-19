<?php

namespace Leon\BswBundle\Controller\BswWorkTaskTrail;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw work task trail
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
        return $this->repo(BswAdminUser::class)->kvp(['name']);
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraTaskId(Arguments $args): array
    {
        $filter = [];
        if ($args->scene === Abs::TAG_PERSISTENCE && !$args->id) {
            $filter = [
                'where' => [$this->expr->gt('bwt.state', ':state')],
                'args'  => ['state' => [0]],
            ];
        }

        $list = $this->repo(BswWorkTask::class)->filters($filter)->lister(
            [
                'limit'  => 0,
                'select' => ['bwt.id', 'bwt.title'],
            ]
        );

        return array_column($list, 'title', 'id');
    }
}