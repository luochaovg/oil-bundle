<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @property Session $session
 */
trait Logout
{
    /**
     * User logout
     *
     * @Route("/user/logout", name="app_user_logout")
     *
     * @return Response
     */
    public function getLogoutAction(): Response
    {
        $this->session->clear();

        return $this->redirectToRoute($this->cnf->route_login);
    }
}