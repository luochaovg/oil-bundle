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
     * @Access(class="danger", title="Dangerous permission, please be careful")
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

    /**
     * @return string
     */
    public function awayEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * Away record
     *
     * @Route("/bsw-config/away/{id}", name="app_bsw_config_away", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function away(int $id): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->doAway(['id' => $id]);
    }
}