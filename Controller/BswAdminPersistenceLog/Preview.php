<?php

namespace Leon\BswBundle\Controller\BswAdminPersistenceLog;

use Leon\BswBundle\Entity\BswAdminPersistenceLog;
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
        return BswAdminPersistenceLog::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_persistence_log_persistence', 'a:plus'),
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
            (new Button('Edit record', 'app_bsw_admin_persistence_log_persistence'))->setArgs(['id' => $current['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-persistence-log/preview", name="app_bsw_admin_persistence_log_preview")
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