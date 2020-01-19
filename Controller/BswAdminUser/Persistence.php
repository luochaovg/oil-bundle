<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Leon\BswBundle\Component\GoogleAuthenticator;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAdminUser::class;
    }

    /**
     * @return array
     */
    public function persistenceTailor(): array
    {
        return [
            Tailor\NewPassword::class => [
                0 => 'password',
            ],
        ];
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-admin-user/persistence/{id}", name="app_bsw_admin_user_persistence", requirements={"id": "\d+"})
     * @Access(class="danger", title=Abs::DANGER_ACCESS)
     *
     * @param int $id
     *
     * @return Response
     */
    public function persistence(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }

    /**
     * Google qr code
     *
     * @Route("/bsw-admin-user/google-qr-code/{id}", name="app_bsw_admin_user_google_qr_code", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     * @throws
     */
    public function googleQrCode(int $id = null): Response
    {
        if (($args = $this->valid(Abs::VW_LOGIN_AS | Abs::V_AJAX)) instanceof Response) {
            return $args;
        }

        /**
         * @var BswAdminUserRepository $bswAdminUser
         */
        $bswAdminUser = $this->repo(BswAdminUser::class);
        $user = $bswAdminUser->find($id);

        $ga = new GoogleAuthenticator();
        if (!$user->googleAuthSecret) {
            $user->googleAuthSecret = $ga->createSecret(16);
            $bswAdminUser->modify(['id' => $id], ['googleAuthSecret' => $user->googleAuthSecret]);
        }

        $app = $this->app(null, false);
        $qrCodeData = $ga->getQrCodeData("{$app}-{$user->name}", $user->googleAuthSecret);

        /**
         * @var QrCode $qrCode
         */
        $qrCode = $this->createQrCode($qrCodeData, 200, 0, ErrorCorrectionLevel::LOW);

        return $this->okayAjax(
            [
                'width'   => 300,
                'title'   => 'Scan the qr-code please',
                'content' => Html::tag(
                    'img',
                    null,
                    [
                        'src'   => $qrCode->writeDataUri(),
                        'class' => 'modal-block-only',
                    ]
                ),
            ]
        );
    }
}