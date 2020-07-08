<?php

namespace Leon\BswBundle\Controller\BswWorkTeam;

use Leon\BswBundle\Entity\BswWorkTeam;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswWorkTeam::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-work-team/persistence/{id}", name="app_bsw_work_team_persistence", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function persistence(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }
}