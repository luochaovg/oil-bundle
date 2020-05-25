<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\Crumbs\Entity\Crumb;

trait WebCrumbs
{
    /**
     * @var array
     */
    public $crumbs = [];

    /**
     * Crumbs builder
     *
     * @param string $route
     * @param array  $allMenuDetail
     *
     * @return array
     */
    public function crumbsBuilder(string $route, array $allMenuDetail = []): array
    {
        return $this->caching(
            function () use ($route, $allMenuDetail) {

                $crumbsMap = $this->parameters('crumbs_map');
                $routes = $this->getRouteCollection();
                $routeClsMap = Helper::arrayColumn($routes, 'desc_cls', 'route');
                $routeFnMap = Helper::arrayColumn($routes, 'desc_fn', 'route');

                /**
                 * In stack
                 *
                 * @param string $route
                 * @param array  $stack
                 *
                 * @return array
                 */
                $inStack = function (string $route, array $stack = []) use (
                    $crumbsMap,
                    $routeClsMap,
                    $routeFnMap,
                    $allMenuDetail,
                    &$inStack
                ) {
                    // manual
                    if (isset($crumbsMap[$route])) {

                        $info = $this->labelWithMenu(
                            $allMenuDetail,
                            $route,
                            $routeFnMap[$route],
                            $routeClsMap[$route]
                        );
                        array_unshift($stack, new Crumb($info, $route));

                        return $inStack($crumbsMap[$route], $stack);
                    }

                    // auto
                    if (!empty($routeFnMap[$route])) {

                        $info = $this->labelWithMenu(
                            $allMenuDetail,
                            $route,
                            $routeFnMap[$route],
                            $routeClsMap[$route]
                        );
                        array_unshift($stack, new Crumb($info, $route));

                        foreach ($this->parameters('crumbs_preview_pre') as $keyword) {
                            if (strpos($route, "_{$keyword}") === false) {
                                continue;
                            }

                            $_route = str_replace("_{$keyword}", '_preview', $route);

                            return $inStack($_route, $stack);
                        }
                    }

                    return $stack;
                };

                $stack = $inStack($route);
                if (($last = count($stack) - 1) <= 0) {
                    return [];
                }

                $crumbsIconMap = $this->parameters('crumbs_keyword_to_icon_map');
                array_unshift($stack, new Crumb('Home', $this->cnf->route_default, $crumbsIconMap['home']));

                /**
                 * @var Crumb[] $stack
                 */
                foreach ($stack as $item) {
                    foreach ($crumbsIconMap as $keyword => $icon) {
                        if (strpos($item->getRoute(), "_{$keyword}") !== false) {
                            $item->setIcon($icon);
                            break;
                        }
                    }
                }

                return $stack;
            }
        );
    }

    /**
     * Crumb changer
     *
     * @param string $title
     * @param string $icon
     * @param int    $index
     *
     * @return bool
     */
    public function crumbsChanger(string $title, ?string $icon = null, int $index = null): bool
    {
        if (empty($this->crumbs)) {
            return false;
        }

        $total = count($this->crumbs);
        $index = $index ?? $total - 1;
        $index = $index < 0 ? $total + $index : $index;

        if (!isset($this->crumbs[$index])) {
            return false;
        }

        /**
         * @var Crumb $crumb
         */
        $crumb = $this->crumbs[$index];

        if (strpos($title, '%s') !== false) {
            $title = sprintf($title, $crumb->getLabel());
        }

        $crumb->setLabel($title);
        if ($icon) {
            $crumb->setIcon($icon);
        }

        return true;
    }
}