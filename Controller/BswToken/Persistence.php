<?php

namespace Leon\BswBundle\Controller\BswToken;

use Leon\BswBundle\Entity\BswToken;
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
        return BswToken::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-token/persistence/{id}", name="app_bsw_token_persistence", requirements={"id": "\d+"})
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