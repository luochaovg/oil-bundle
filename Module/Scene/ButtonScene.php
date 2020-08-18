<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;

class ButtonScene extends Button
{
    /**
     * @param null|string $title
     *
     * @return ButtonScene
     */
    public function setTitle(?string $title = null)
    {
        return $this->appendArgs(['title' => $title]);
    }

    /**
     * @return ButtonScene
     */
    public function setNoTitle()
    {
        return $this->appendArgs(['title' => false]);
    }

    /**
     * @return ButtonScene
     */
    public function setAutoTitle()
    {
        return $this->appendArgs(['title' => $this->getLabel() ?: null]);
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentModal(string $content)
    {
        return $this->setClick('showModal')->appendArgs(['content' => $content]);
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentModalNoTitle(string $content)
    {
        return $this->setContentModal($content)->setNoTitle();
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentModalAutoTitle(string $content)
    {
        return $this->setContentModal($content)->setAutoTitle();
    }

    /**
     * @param string $content
     *
     * @return  ButtonScene
     */
    public function setContentDrawer(string $content)
    {
        return $this->setClick('showDrawer')->appendArgs(['content' => $content]);
    }

    /**
     * @param string $content
     *
     * @return  ButtonScene
     */
    public function setContentDrawerNoTitle(string $content)
    {
        return $this->setContentDrawer($content)->setNoTitle();
    }

    /**
     * @param string $content
     *
     * @return  ButtonScene
     */
    public function setContentDrawerAutoTitle(string $content)
    {
        return $this->setContentDrawer($content)->setAutoTitle();
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setRouteModal(string $route)
    {
        return $this->setRoute($route)->setClick('showIFrame');
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setRouteModalNoTitle(string $route)
    {
        return $this->setRouteModal($route)->setNoTitle();
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setRouteModalAutoTitle(string $route)
    {
        return $this->setRouteModal($route)->setAutoTitle();
    }

    /**
     * @param string $route
     *
     * @return  ButtonScene
     */
    public function setRouteDrawer(string $route)
    {
        return $this->setRouteModal($route)->appendArgs(['shape' => Abs::SHAPE_DRAWER]);
    }

    /**
     * @param string $route
     *
     * @return  ButtonScene
     */
    public function setRouteDrawerNoTitle(string $route)
    {
        return $this->setRouteDrawer($route)->setNoTitle();
    }

    /**
     * @param string $route
     *
     * @return  ButtonScene
     */
    public function setRouteDrawerAutoTitle(string $route)
    {
        return $this->setRouteDrawer($route)->setAutoTitle();
    }

    /**
     * @param int|null $width
     * @param int|null $height
     *
     * @return ButtonScene
     */
    public function setWidthHeight(?int $width = null, ?int $height = null)
    {
        return $this->appendArgs(['width' => $width, 'height' => $height]);
    }

    /**
     * @param int $min
     *
     * @return ButtonScene
     */
    public function setAutoMinHeight(int $min)
    {
        return $this->appendArgs(['minHeight' => max($min, 0)]);
    }

    /**
     * @param int $max
     *
     * @return ButtonScene
     */
    public function setAutoMaxHeight(int $max)
    {
        return $this->appendArgs(['maxHeight' => $max]);
    }

    /**
     * @param int $offset
     *
     * @return ButtonScene
     */
    public function setAutoHeightOffset(int $offset = 100)
    {
        if (!($height = $this->getArgsItem('height'))) {
            return $this;
        }

        return $this->setAutoMinHeight($height - $offset)->setAutoMaxHeight($height + $offset);
    }

    /**
     * @param bool $allow
     *
     * @return ButtonScene
     */
    public function setAutoHeightOverOffset(bool $allow = false)
    {
        return $this->appendArgs(['overOffset' => $allow ? 'yes' : 'no']);
    }

    /**
     * @param int $id
     *
     * @return ButtonScene
     */
    public function setId(int $id)
    {
        return $this->appendArgs(['id' => $id]);
    }

    /**
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function setModalCharmSortScene(string $route, int $id)
    {
        return $this->setType(Abs::THEME_ELE_PRIMARY_OL)
            ->setIcon('b:icon-icon-test88')
            ->setSize(Abs::SIZE_SMALL)
            ->setShape(Abs::SHAPE_ROUND)
            ->setWidthHeight(Abs::MEDIA_XS, 222)
            ->setRouteModalNoTitle($route)
            ->setId($id);
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setModalOperateNewlyScene(string $route)
    {
        return $this->setIcon('a:plus')
            ->setRouteModalAutoTitle($route)
            ->setWidthHeight(Abs::MEDIA_SM, 650);
    }
}