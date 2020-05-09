<?php

namespace Leon\BswBundle\Controller\BswAdminAccessControl;

use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Module\Bsw\Arguments;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminAccessControl::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_access_control_persistence', 'a:plus'),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Edit record', 'app_bsw_admin_access_control_persistence'))->setArgs(
                ['id' => $args->item['id']]
            ),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-access-control/preview", name="app_bsw_admin_access_control_preview")
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