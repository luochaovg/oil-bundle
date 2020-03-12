<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
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