<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Expr                $expr
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
    public function previewAnnotation(): array
    {
        return [
            'id'    => ['fixed' => false],
            'value' => [
                'sort'   => 0.1,
                'render' => null,
                'align'  => null,
            ],
        ];
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
     * @return array
     */
    public function previewQueryParent()
    {
        return [
            'where' => [$this->expr->eq('bam.menuId', ':menuId')],
            'args'  => ['menuId' => [0]],
            'sort'  => ['bam.sort' => Abs::SORT_ASC],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewQueryChildren(Arguments $args)
    {
        return [
            'where' => [$this->expr->eq('bam.menuId', ':menuId')],
            'args'  => ['menuId' => [$args->parent]],
            'sort'  => ['bam.sort' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Encase', null, $this->cnf->icon_two_box))
                ->setType(Abs::THEME_DANGER)
                ->setSelector(Abs::SELECTOR_CHECKBOX)
                ->setRoute('app_bsw_admin_menu_multiple_encase')
                ->setClick('multipleAction')
                ->setConfirm($this->messageLang('Are you sure')),

            (new Button('Sure', null, $this->cnf->icon_submit_form))
                ->setSelector(Abs::SELECTOR_RADIO)
                ->setClick('fillParentForm')
                ->setScene(Abs::SCENE_IFRAME)
                ->setArgs(
                    [
                        'repair'   => $this->getArgs('repair'),
                        'selector' => 'id',
                    ]
                ),

            new Button('New record', 'app_bsw_admin_menu_persistence', $this->cnf->icon_newly),
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
            (new Button('Sort'))
                ->setType(Abs::THEME_DEFAULT)
                ->setRoute('app_bsw_admin_menu_sort')
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => Abs::MEDIA_MIN,
                        'height' => 220,
                        'title'  => false,
                    ]
                ),
            (new Button('Edit record', 'app_bsw_admin_menu_persistence'))->setArgs(['id' => $args->item['id']]),
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

        return $this->showPreview(['parentField' => true]);
    }
}