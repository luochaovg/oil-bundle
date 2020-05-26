<?php

namespace Leon\BswBundle\Controller\BswCommandQueue;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
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
     * @return array
     */
    public function previewTailor(): array
    {
        return [
            Tailor\AttachmentLink::class => [
                0 => 'fileAttachmentId',
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewQuery(): array
    {
        return [
            'order' => [
                'bcq.state'        => Abs::SORT_ASC,
                'bcq.resourceNeed' => Abs::SORT_ASC,
            ],
        ];
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
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Edit record', 'app_bsw_command_queue_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        $condition = Helper::parseJsonString($args->original['condition']);

        if (isset($condition['entity'])) {
            $condition['entity'] = base64_decode($condition['entity']);
        }

        if (isset($condition['query'])) {
            $condition['query'] = '-- CONDITION FOR EXPORT --';
        }

        $args->hooked['condition'] = Helper::formatPrintJson($condition, 4, ': ');

        return $args->hooked;
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

        return $this->showPreview(['dynamic' => 5]);
    }
}