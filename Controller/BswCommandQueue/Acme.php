<?php

namespace Leon\BswBundle\Controller\BswCommandQueue;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw command queue
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
}