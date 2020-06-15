<?php

namespace Leon\BswBundle\Controller\BswWorkTeam;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw work team
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
}