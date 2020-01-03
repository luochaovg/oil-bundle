<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property Session|SessionInterface $session
 * @property LoggerInterface          $logger
 */
trait NumberCaptcha
{
    /**
     * Show captcha
     *
     * @Route("/captcha", name="app_captcha")
     *
     * @return Response|bool
     */
    public function numberCaptcha()
    {
        $digit = intval($this->getArgs('digit') ?? $this->parameter('backend_captcha_digit'));
        if ($digit < 3 || $digit > 6) {
            $digit = 4;
        }

        $builder = new CaptchaBuilder(null, new PhraseBuilder($digit));
        $builder->setBackgroundColor(236, 245, 255);
        $builder->build(120, 40);

        $this->session->set($this->skCaptcha, $builder->getPhrase());
        $builder->output();

        return new Response(null, 200, ['Content-type' => 'image/jpeg']);
    }
}