<?php

namespace Leon\BswBundle\Controller\BswConfig;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswConfig;
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
        return BswConfig::class;
    }

    /**
     * @return array
     */
    public function previewQuery()
    {
        return [
            'order' => ['bc.key' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_config_persistence', $this->cnf->icon_newly),
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
            (new Button('Edit record', 'app_bsw_config_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-config/preview", name="app_bsw_config_preview")
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