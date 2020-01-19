<?php

namespace Leon\BswBundle\Controller\BswCommandQueue;

use Leon\BswBundle\Entity\BswCommandQueue;
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
        return BswCommandQueue::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_command_queue_persistence', 'a:plus'),
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
            (new Button('Edit record', 'app_bsw_command_queue_persistence'))->setArgs(['id' => $current['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-command-queue/preview", name="app_bsw_command_queue_preview")
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