<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

/**
 * @property AdapterInterface $cache
 */
trait CleanBackend
{
    /**
     * Clean backend cache
     *
     * @Route("/cache/backend", name="app_clean_backend")
     * @Access()
     *
     * @return Response
     * @throws
     */
    public function getCleanBackendAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->cache->clear();

        return $this->responseSuccess('Cache clear success', [], $this->reference());
    }
}