<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Preview
{
    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            Abs::TR_ACT => ['width' => 234],
        ];
    }

    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminUser::class;
    }

    /**
     * @return array
     */
    public function previewTailor(): array
    {
        return [
            Tailor\AttachmentImage::class => [
                0 => 'avatarAttachmentId',
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_user_persistence', 'a:plus'),
        ];
    }

    /**
     * @param array $current
     * @param array $hooked
     * @param array $origin
     *
     * @return Button[]
     */
    public function previewRecordOperates(array $current, array $hooked, array $origin): array
    {
        return [
            (new Button('Edit record'))
                ->setRoute('app_bsw_admin_user_persistence')
                ->setArgs(['id' => $current['id']]),

            (new Button('Google qr code'))
                ->setType(Button::THEME_DEFAULT)->setRoute('app_bsw_admin_user_google_qr_code')
                ->setArgs(['id' => $current['id']])
                ->setClick('showModalAfterRequest'),

            (new Button('Grant authorization for user'))
                ->setRoute('app_bsw_admin_access_control_grant')
                ->setType(Button::THEME_DANGER)
                ->setArgs(['id' => $current['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-user/preview", name="app_bsw_admin_user_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}