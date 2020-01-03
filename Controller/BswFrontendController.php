<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Error;

class BswFrontendController extends BswWebController
{
    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_FRONTEND;

    /**
     * @var bool
     */
    protected $webSrc = true;

    /**
     * @var string
     */
    protected $skUser = 'frontend-user-sk';

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        if ($this->webSrc) {
            $this->appendSrcJs(Abs::JS_WEB);
            $this->appendSrcCss(Abs::CSS_WEB);
        }
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error
     */
    protected function webShouldAuth(array $args)
    {
        return new ErrorAuthorization();
    }

    /**
     * Access builder
     *
     * @param object $usr
     *
     * @return array
     */
    protected function accessBuilder($usr): array
    {
        return [];
    }
}