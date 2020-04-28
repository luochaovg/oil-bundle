<?php

namespace Leon\BswBundle\Module\Bsw\Menu;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Menu\Entity\Menu;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'menu';
    }

    /**
     * @return string|null
     */
    public function twig(): ?string
    {
        return '@LeonBsw/limbs/menu';
    }

    /**
     * @return array
     */
    public function css(): ?array
    {
        return null;
    }

    /**
     * @return array
     */
    public function javascript(): ?array
    {
        return ['diy;module/scaffold.js'];
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * @return array
     */
    protected function listMenu(): array
    {
        return $this->web->caching(
            function () {
                $filter = [
                    'limit'  => 0,
                    'select' => [
                        'bam.id',
                        'bam.menuId',
                        'bam.routeName',
                        'bam.icon',
                        'bam.value',
                        'bam.javascript',
                        'bam.jsonParams',
                    ],
                    'where'  => [$this->input->expr->eq('bam.state', ':state')],
                    'args'   => ['state' => [Abs::NORMAL]],
                    'sort'   => ['bam.sort' => Abs::SORT_ASC],
                ];

                $list = $this->web->repo(BswAdminMenu::class)->lister($filter);
                $menuList = [];

                foreach ($list as $item) {
                    $menu = new Menu();
                    $menu->attributes($item);
                    array_push($menuList, $menu);
                }

                return $menuList;
            }
        );
    }

    /**
     * @return array
     */
    protected function menuBuilder(): array
    {
        $menu = $slaveMenuDetail = [];
        $parent = $current = $masterIndex = 0;

        $menuList = $this->listMenu();
        if (empty($menuList)) {
            return [$menu, $menu, $slaveMenuDetail, $parent, $current];
        }

        // current and parent
        $currentMap = $this->web->parameters('menus_same_current_map');
        $parentMap = $this->web->parameters('menus_same_parent_map');

        foreach ($menuList as $item) {

            /**
             * @var Menu $item
             */
            $route = trim($item->getRouteName());

            // access control
            if ($route && empty($this->input->access[$route])) {
                continue;
            }

            $args = Helper::parseJsonString($item->getJsonParams() ?? '', []);

            // route path
            if ($route) {
                $item->setUrl($this->web->urlSafe($route, $args, 'Menu route'));
            }

            // javascript
            if ($javascript = $item->getJavascript()) {
                $item->setArgs(array_merge(['function' => $javascript], $args));
            }

            $menu[$item->getMenuId()][$item->getId()] = $item;
            if ($item->getMenuId() !== $masterIndex) {
                $slaveMenuDetail[$item->getRouteName()] = [
                    'info'          => $item->getValue(),
                    'parentMenuId'  => $item->getMenuId(),
                    'currentMenuId' => $item->getId(),
                ];
            }

            $currentRoute = $this->input->route;
            if (isset($currentMap[$currentRoute])) {
                $currentRoute = $currentMap[$currentRoute];
            } elseif (isset($parentMap[$currentRoute])) {
                $sameParentOnly = true;
                $currentRoute = $parentMap[$currentRoute];
            }

            foreach ($this->web->parameters('crumbs_preview_pre') as $keyword) {
                $currentRoute = preg_replace("/_{$keyword}$/i", '_preview', $currentRoute);
            }

            if ($route == $currentRoute) {
                $parent = $item->getMenuId() ?: $masterIndex;
                $current = empty($sameParentOnly) ? $item->getId() : 0;
            }
        }

        $masterMenu = Helper::dig($menu, $masterIndex);
        $slaveMenu = $menu;

        $_masterMenu = [];
        foreach ($masterMenu as $index => $item) {
            if (!empty($slaveMenu[$index]) || !empty($item->getRouteName())) {
                $_masterMenu[$index] = $item;
            }
        }

        // correct parent
        if (empty($parent)) {
            $parent = $slaveMenuDetail[$this->input->route]['parentMenuId'] ?? 0;
        }

        // correct current
        if (empty($current)) {
            $current = $slaveMenuDetail[$this->input->route]['currentMenuId'] ?? 0;
        }

        return [$_masterMenu, $slaveMenu, $slaveMenuDetail, $parent, $current];
    }

    /**
     * @return ArgsOutput
     */
    public function logic(): ArgsOutput
    {
        $output = new Output();
        [
            $output->masterMenu,
            $output->slaveMenu,
            $output->slaveMenuDetail,
            $output->parent,
            $output->current,
        ] = $this->menuBuilder();

        $output = $this->caller(
            $this->method . ucfirst($this->name()),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            [$output]
        );

        return $output;
    }
}