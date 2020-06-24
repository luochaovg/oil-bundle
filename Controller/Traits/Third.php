<?php

namespace Leon\BswBundle\Controller\Traits;

use EasyWeChat\Factory as WxFactory;
use Yansongda\Pay\Gateways\Alipay;
use Yansongda\Pay\Pay as WxAliPayment;
use EasyWeChat\OfficialAccount\Application as WxOfficial;
use EasyWeChat\Payment\Application as WxPayment;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Session\Session;
use Gregwar\Captcha\CaptchaBuilder;
use ParagonIE\ConstantTime\Base32;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Monolog\Logger;
use App\Kernel;
use OTPHP\TOTP;

/**
 * @property Kernel  $kernel
 * @property Session $session
 * @property Logger  $logger
 */
trait Third
{
    /**
     * Create qr code
     *
     * @param string $content
     * @param int    $qrWidth
     * @param string $level
     * @param int    $qrMargin
     * @param array  $fColor
     * @param array  $bColor
     * @param string $logoFile
     * @param int    $logoWidth
     *
     * @return QrCode
     * @throws
     */
    public function createQrCode(
        string $content,
        int $qrWidth = 256,
        int $qrMargin = 10,
        ?string $level = null,
        ?array $fColor = null,
        ?array $bColor = null,
        ?string $logoFile = null,
        ?int $logoWidth = null
    ) {

        $qrCode = new QrCode($content);
        $qrCode->setSize($qrWidth);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin($qrMargin);
        $qrCode->setEncoding('utf-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel($level ?: ErrorCorrectionLevel::MEDIUM));
        $qrCode->setForegroundColor($fColor ?: ['r' => 70, 'g' => 70, 'b' => 70]);
        $qrCode->setBackgroundColor($bColor ?: ['r' => 255, 'g' => 255, 'b' => 255]);

        if (!empty($logoFile) && realpath($logoFile)) {
            $qrCode->setLogoPath($logoFile);
            $logoWidth = $logoWidth ?: $qrWidth / 4;
            $qrCode->setLogoWidth($logoWidth);
        }

        $qrCode->setValidateResult(false);

        return $qrCode;
    }

    /**
     * Time based One-Time Password Algorithm (T.O.T.P)
     *
     * @param string $uid
     * @param int    $digits
     * @param int    $time
     *
     * @return array
     */
    public function TOTPToken(string $uid, int $digits = 6, int $time = null): array
    {
        $epoch = 0;
        $second = 120;
        $time = $time ?? $this->cnf->totp_token_second ?? $second;
        $time = $time ?: $second;

        $secret = Base32::encode($this->parameter('salt') . $uid);
        $token = TOTP::create($secret, $time, 'sha1', $digits, $epoch);

        $now = time();
        $timeCode = intval(($now - $epoch) / $time);
        $from = $timeCode * $time;
        $to = $from + $time;

        return [
            $token->at($now),
            date(Abs::FMT_FULL, $from),
            date(Abs::FMT_FULL, $to),
        ];
    }

    /**
     * Check captcha
     *
     * @param string $input
     *
     * @return bool
     */
    public function checkCaptcha(string $input): bool
    {
        $captcha = $this->session->get($this->skCaptcha);
        $this->logger->debug("Number captcha in server {$captcha} and user input {$input}");

        return (new CaptchaBuilder($captcha))->testPhrase($input);
    }

    /**
     * Get WeChat official account
     *
     * @param string $flag
     *
     * @return WxOfficial
     */
    public function wxOfficial(string $flag = 'default'): WxOfficial
    {
        return WxFactory::officialAccount($this->parameter("wx_official_{$flag}"));
    }

    /**
     * Get WeChat payment
     *
     * @param string $flag
     * @param bool   $sandbox
     *
     * @return WxPayment
     */
    public function wxPayment(string $flag = 'default', bool $sandbox = false): WxPayment
    {
        $config = $this->parameter("wx_payment_{$flag}");
        $sandbox = $sandbox ? ['sandbox' => true] : [];

        return WxFactory::payment(array_merge($config, $sandbox));
    }

    /**
     * Get ali payment
     *
     * @param string $flag
     * @param bool   $sandbox
     *
     * @return Alipay
     */
    public function aliPayment(string $flag = 'default', bool $sandbox = false): Alipay
    {
        $config = $this->parameter("ali_payment_{$flag}");
        $sandbox = $sandbox ? ['mode' => 'dev'] : [];

        return WxAliPayment::alipay(array_merge($config, $sandbox));
    }
}