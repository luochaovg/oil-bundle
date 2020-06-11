<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Component\HttpFoundation\Response;

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
            $lang = $this->langLatest($this->langMap, 'en');

            $this->appendSrcJs(
                [Abs::JS_MOMENT_LANG[$lang], Abs::JS_LANG[$lang], Abs::JS_WEB],
                Abs::POS_BOTTOM,
                '',
                true
            );
            $this->appendSrcCss(
                [Abs::CSS_WEB],
                Abs::POS_BOTTOM,
                '',
                true
            );
        }
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error|Response
     */
    protected function webShouldAuth(array $args)
    {
        return [];
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