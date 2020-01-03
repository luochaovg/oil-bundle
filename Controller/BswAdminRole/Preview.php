<?php

namespace Leon\BswBundle\Controller\BswAdminRole;

use Leon\BswBundle\Entity\BswAdminRole;
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
            Abs::TR_ACT => ['width' => 150],
        ];
    }

    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminRole::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_role_persistence', 'a:plus'),
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
                ->setRoute('app_bsw_admin_role_persistence')
                ->setArgs(['id' => $current['id']]),

            (new Button('Grant authorization for role'))
                ->setRoute('app_bsw_admin_role_access_control_grant')
                ->setType(Button::THEME_DANGER)
                ->setArgs(['id' => $current['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-role/preview", name="app_bsw_admin_role_preview")
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