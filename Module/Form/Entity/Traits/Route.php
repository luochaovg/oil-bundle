<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Route
{
    /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $routeForAccess;

    /**
     * @param string $route
     *
     * @return string
     */
    public function getRoute(string $route = ''): string
    {
        return $this->route ?? $route;
    }

    /**
     * @param string $route
     *
     * @return $this
     */
    public function setRoute(string $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param string $routeForAccess
     *
     * @return string
     */
    public function getRouteForAccess(string $routeForAccess = ''): string
    {
        return $this->routeForAccess ?? $this->route ?? $routeForAccess;
    }

    /**
     * @param string $routeForAccess
     *
     * @return $this
     */
    public function setRouteForAccess(string $routeForAccess)
    {
        $this->routeForAccess = $routeForAccess;

        return $this;
    }
}