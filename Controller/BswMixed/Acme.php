<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw mixed
 */
class Acme extends BswBackendController
{
    use CleanBackend;
    use Export;
    use Login;
    use Logout;
    use NumberCaptcha;
    use Profile;
    use SiteIndex;
    use Telegram;
    use ThirdMessage;
}