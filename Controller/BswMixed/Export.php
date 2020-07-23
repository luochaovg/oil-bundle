<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Export
{
    /**
     * @return string
     */
    public function exportEntity(): string
    {
        return BswCommandQueue::class;
    }

    /**
     * @return array
     */
    public function exportAnnotation(): array
    {
        $condition = $this->getArgs(['entity', 'query', 'time', 'signature']);
        $condition = array_map('urldecode', $condition);

        return [
            'command'   => [
                'value' => 'mission:export-preview',
                'hide'  => true,
            ],
            'condition' => [
                'value' => Helper::formatPrintJson($condition, 4, ': '),
                'hide'  => true,
            ],
            'remark'    => false,
            'state'     => false,
        ];
    }

    /**
     * Export record
     *
     * @Route("/export", name="app_export")
     * @Access(same="app_bsw_command_queue_persistence")
     *
     * @return Response
     */
    public function export(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $nextRoute = $this->getHistoryRoute(-2);

        return $this->showPersistence(
            [
                'nextRoute'      => $nextRoute,
                'messageHandler' => function (Message $message) {
                    $this->appendResult(
                        [
                            'width'      => 400,
                            'title'      => $this->messageLang('Newly mission queue done'),
                            'status'     => Abs::RESULT_STATUS_SUCCESS,
                            'cancelShow' => true,
                            'okText'     => $this->twigLang('Look up'),
                            'ok'         => 'redirect',
                            'extra'      => ['location' => $this->url('app_bsw_command_queue_preview')],
                        ]
                    );

                    return $message->setMessage('');
                },
            ]
        );
    }
}