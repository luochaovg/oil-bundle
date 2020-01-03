<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property TranslatorInterface $translator
 */
trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminMenu::class;
    }

    /**
     * @return array
     */
    public function previewQuery()
    {
        return [
            'sort' => ['bam.sort' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Encase', null, 'b:icon-similarproduct'))
                ->setType(Button::THEME_DANGER)
                ->setSelector(Abs::SELECTOR_CHECKBOX)
                ->setRoute('app_bsw_admin_menu_multiple_encase')
                ->setClick('multipleAction')
                ->setArgs(['uuid' => $this->uuid])
                ->setConfirm($this->translator->trans('Are you sure')),

            (new Button('Sure', null, 'b:icon-rfq'))
                ->setSelector(Abs::SELECTOR_RADIO)
                ->setClick('fillParentForm')
                ->setScene(Button::SCENE_IFRAME)
                ->setArgs(['uuid' => $this->uuid, 'fill' => $this->getArgs('fill')]),

            new Button('New record', 'app_bsw_admin_menu_persistence', 'a:plus'),
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
            (new Button('Edit record', 'app_bsw_admin_menu_persistence'))->setArgs(['id' => $current['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-menu/preview", name="app_bsw_admin_menu_preview")
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