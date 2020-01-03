<?php

namespace Leon\BswBundle\Controller\BswAdminLogin;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw admin login
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;

    /**
     * @param array $enum
     * @return array
     * @throws
     */
    public function acmeEnumExtraUserId(array $enum): array
    {
        $role = $this->repo(BswAdminUser::class)->kvp(['name']);
        $role = [0 => Abs::NIL] + $role;

        return $role;
    }
}