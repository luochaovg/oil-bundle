<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCommandQueue;
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
        $entity = $this->getArgs('entity');
        $query = $this->getArgs('query');

        if (empty($entity) || empty($query)) {
            $condition = null;
        } else {
            $condition = compact('entity', 'query');
        }

        return [
            'command'   => [
                'value' => 'mission:export-preview',
            ],
            'condition' => [
                'value' => Helper::formatPrintJson($condition, 4, ': '),
            ],
        ];
    }

    /**
     * Export record
     *
     * @Route("/export", name="app_export")
     * @Access()
     *
     * @return Response
     */
    public function export(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $nextRoute = $this->getHistoryRoute(-2);

        return $this->showPersistence(['nextRoute' => $nextRoute, 'i18nNewly' => 'Newly mission queue done']);
    }
}