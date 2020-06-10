<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw admin menu
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraMenuId(Arguments $args): array
    {
        $filter = [
            'where' => [
                $this->expr->eq('kvp.menuId', ':parent'),
                $this->expr->eq('kvp.routeName', ':route'),
            ],
            'args'  => [
                'parent' => [0],
                'route'  => ['', false],
            ],
            'sort'  => ['kvp.sort' => Abs::SORT_ASC],
        ];

        $menu = $this->repo(BswAdminMenu::class)->kvp(['value'], Abs::PK, null, $filter);
        $menu = [0 => '(Top Menu)'] + $menu;

        return $menu;
    }
}