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
     * @var array
     */
    public $correctCrumbs = [];

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
     * Change crumbs
     *
     * @param string $title
     * @param string $icon
     * @param int    $index
     *
     * @return int
     */
    public function changeCrumbs(string $title, ?string $icon = null, int $index = null): int
    {
        return array_push(
            $this->correctCrumbs,
            [
                'mode'  => 'change',
                'title' => $title,
                'icon'  => $icon,
                'index' => $index,
            ]
        );
    }

    /**
     * Append crumbs
     *
     * @param string $title
     * @param string $icon
     *
     * @return int
     */
    public function appendCrumbs(string $title, ?string $icon = null): int
    {
        return array_push(
            $this->correctCrumbs,
            [
                'mode'  => 'append',
                'title' => $title,
                'icon'  => $icon,
            ]
        );
    }

    /**
     * Correct crumbs
     */
    public function correctCrumbs()
    {
        if (empty($this->crumbs)) {
            return;
        }

        $total = count($this->crumbs);
        foreach ($this->correctCrumbs as $item) {

            /**
             * @var string $mode
             * @var string $title
             * @var string $icon
             * @var int    $index
             */
            extract($item);

            if ($mode == 'append') {
                array_push($this->crumbs, new Crumb($title, null, $icon));
                continue;
            }

            $index = $index ?? $total - 1;
            $index = $index < 0 ? $total + $index : $index;
            if (!isset($this->crumbs[$index])) {
                return;
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
        }
    }
}