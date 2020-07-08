<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\GoogleAuthenticator;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAccountFrozen;
use Leon\BswBundle\Module\Error\Entity\ErrorCaptcha;
use Leon\BswBundle\Module\Error\Entity\ErrorGoogleCaptcha;
use Leon\BswBundle\Module\Error\Entity\ErrorMetaData;
use Leon\BswBundle\Module\Error\Entity\ErrorPassword;
use Leon\BswBundle\Module\Error\Entity\ErrorProhibitedCountry;
use Leon\BswBundle\Module\Error\Entity\ErrorUsername;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Input;
use Leon\BswBundle\Module\Form\Entity\Password;
use Leon\BswBundle\Module\Validator\Entity\Rsa;
use Leon\BswBundle\Component\Rsa as ComponentRsa;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Monolog\Logger;

/**
 * @property Session $session
 * @property Logger  $logger
 */
trait Login
{
    /**
     * User login
     *
     * @Route("/user/login", name="app_user_login")
     *
     * @return Response
     */
    public function getLoginAction(): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $this->currentSrc('diy;layout/login');
        $this->appendSrcJsWithKey('rsa', Abs::JS_RSA);

        $form = [
            'account' => (new Input())
                ->setPlaceholder('Phone number')
                ->setIcon($this->cnf->icon_user)
                ->setIconAttribute(['slot' => 'prefix'])
                ->setName("account-4-" . ($this->cnf->app_name ?? 'bsw')),

            'password' => (new Password())
                ->setPlaceholder('Password')
                ->setIcon($this->cnf->icon_lock)
                ->setIconAttribute(['slot' => 'prefix']),

            'captcha' => (new Input())
                ->setPlaceholder('Captcha')
                ->setIcon($this->cnf->icon_captcha)
                ->setIconAttribute(['slot' => 'prefix']),

            'submit' => (new Button())
                ->setLabel('SIGN IN')
                ->setHtmlType(Abs::TYPE_SUBMIT)
                ->setBlock(true)
                ->setBindLoading('btnLoading'),
        ];

        if ($this->parameter('backend_with_google_secret')) {
            $form['googleCaptcha'] = (new Input())
                ->setPlaceholder('Google dynamic captcha')
                ->setIcon($this->cnf->icon_captcha)
                ->setIconAttribute(['slot' => 'prefix']);
        }

        return $this->show($form, 'layout/login.html');
    }

    /**
     * @return array
     */
    protected function validatorExtraArgs(): array
    {
        return [Rsa::class => $this->component(ComponentRsa::class)];
    }

    /**
     * User login handler
     *
     * @Route("/user/sign-in", name="app_user_login_handler", methods="POST")
     *
     * @I("account", rules="phone")
     * @I("password", rules="rsa|password")
     * @I("captcha", rules="length,4")
     * @I("google_captcha", rules="~|length,6")
     *
     * @return Response
     * @throws
     */
    public function postSignInAction(): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING | Abs::V_AJAX)) instanceof Response) {
            return $args;
        }

        /**
         * number captcha
         */

        if (!$this->checkCaptcha($args->captcha)) {
            return $this->failedAjax(new ErrorCaptcha());
        }

        /**
         * @var BswAdminUserRepository $bswAdminUser
         */
        $bswAdminUser = $this->repo(BswAdminUser::class);
        $user = $bswAdminUser->findOneBy(['phone' => $args->account]);

        /**
         * user valid
         */

        if (empty($user)) {
            return $this->failedAjax(new ErrorUsername());
        }

        /**
         * user state
         */

        if ($user->state !== Abs::NORMAL) {
            return $this->failedAjax(new ErrorAccountFrozen());
        }

        /**
         * google captcha
         */

        if ($this->parameter('backend_with_google_secret')) {

            if (empty($user->googleAuthSecret) || strlen($user->googleAuthSecret) !== 16) {
                return $this->failedAjax(new ErrorMetaData());
            }

            $ga = new GoogleAuthenticator();
            $googleCaptcha = $ga->verifyCode($user->googleAuthSecret, $args->google_captcha, 2);
            if (!$googleCaptcha) {
                return $this->failedAjax(new ErrorGoogleCaptcha());
            }
        }

        /**
         * password
         */

        $password = $this->password($args->password);
        if ($user->password !== $password) {
            return $this->failedAjax(new ErrorPassword());
        }

        $ip = $this->getClientIp();

        /**
         * ip limit
         */
        if ($this->parameter('backend_ip_limit')) {
            if (!Helper::ipInWhiteList($ip, $this->parameters('backend_allow_ips'))) {
                $this->logger->error("The ip is prohibited: {$ip}");

                return $this->failedAjax(new ErrorProhibitedCountry());
            }
        }

        $this->loginAdminUser($user, $ip);

        /**
         * fallback
         */

        $fallback = $this->session->getFlashBag()->get(Abs::TAG_FALLBACK);
        $fallback = end($fallback);
        $fallback = $fallback ?: $this->urlSafe($this->cnf->route_default);

        return $this->okayAjax(['href' => $fallback], 'Login success');
    }
}