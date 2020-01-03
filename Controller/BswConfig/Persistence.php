<?php

namespace Leon\BswBundle\Controller\BswConfig;

use Leon\BswBundle\Entity\BswConfig;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswConfig::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-config/persistence/{id}", name="app_bsw_config_persistence", requirements={"id": "\d+"})
     * @Access(class="danger", tips=Abs::DANGER_ACCESS)
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