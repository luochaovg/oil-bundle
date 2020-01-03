<?php

namespace Leon\BswBundle\Controller\BswAdminRoleAccessControl;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw admin role access control
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
    use Grant;

    /**
     * @param array $enum
     * @return array
     * @throws
     */
    public function acmeEnumExtraRoleId(array $enum): array
    {
        $role = $this->repo(BswAdminRole::class)->kvp(['name']);
        $role = [0 => Abs::NIL] + $role;

        return $role;
    }
}