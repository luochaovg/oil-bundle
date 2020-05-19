<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
use Leon\BswBundle\Module\Form\Entity\Number;
use Leon\BswBundle\Repository\BswAdminMenuRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

/**
 * @property Expr $expr
 */
trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAdminMenu::class;
    }

    /**
     * @return array
     */
    public function persistenceAnnotation(): array
    {
        $iconA = (new Button('a:class'))
            ->setType(Button::THEME_DEFAULT)
            ->setSize(Button::SIZE_SMALL)
            ->setArgs(['location' => $this->cnf->ant_icon_url, 'window' => true]);
        $iconA = $this->renderPart('@LeonBsw/form/button', ['form' => $iconA]);

        $iconB = (new Button('b:symbol'))
            ->setType(Button::THEME_DEFAULT)
            ->setSize(Button::SIZE_SMALL)
            ->setArgs(['location' => $this->cnf->font_symbol_url, 'window' => true]);
        $iconB = $this->renderPart('@LeonBsw/form/button', ['form' => $iconB]);

        return [
            'icon' => [
                'title' => "{$iconA} / {$iconB}",
            ],
        ];
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-admin-menu/persistence/{id}", name="app_bsw_admin_menu_persistence", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function persistence(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }

    /**
     * @return string
     */
    public function sortEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function sortAnnotationOnly(): array
    {
        return ['id' => true, 'sort' => true];
    }

    /**
     * Sort record
     *
     * @Route("/bsw-admin-menu/sort/{id}", name="app_bsw_admin_menu_sort", requirements={"id": "\d+"})
     * @Access(same="app_bsw_admin_menu_persistence")
     *
     * @param int $id
     *
     * @return Response
     */
    public function sort(int $id = null): Response
    {
        return $this->persistence($id);
    }

    /**
     * Multiple encase
     *
     * @Route("/bsw-admin-menu/multiple-encase", name="app_bsw_admin_menu_multiple_encase")
     * @Access()
     *
     * @I("ids", rules="arr")
     *
     * @return Response
     */
    public function multipleEncase(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $ids = array_map('intval', array_column($args->ids, 'id'));

        /**
         * @var BswAdminMenuRepository $menu
         */
        $menu = $this->repo(BswAdminMenu::class);
        $effect = $menu->updater(
            [
                'where' => [$this->expr->in('bam.id', $ids)],
                'set'   => ["bam.state" => ':state'],
                'args'  => ['state' => [Abs::CLOSE]],
            ]
        );

        if ($effect === false) {
            return $this->responseError(new ErrorDbPersistence(), $menu->pop());
        }

        return $this->responseSuccess(
            'Multiple action success, total {{ num }}',
            ['{{ num }}' => $effect, 'href' => '']
        );
    }
}