<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
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
    public function setNoIcon()
    {
        return $this->setIcon(null);
    }

    /**
     * @param bool $want
     *
     * @return ButtonScene
     */
    public function setIWantToKnowHeight(bool $want = true)
    {
        return $this->appendArgs(['debugRealHeight' => $want]);
    }

    /**
     * @return ButtonScene
     */
    public function setAutoTitle()
    {
        return $this->appendArgs(['title' => $this->getLabel() ?: null]);
    }

    /**
     * @param string $shape
     *
     * @return ButtonScene
     */
    public function setShowIframeShape(string $shape = Abs::SHAPE_MODAL)
    {
        return $this->appendArgs(['shape' => $shape]);
    }

    /**
     * @param int|null $width
     *
     * @return ButtonScene
     */
    public function setWidth(?int $width = null)
    {
        return $this->appendArgs(['width' => $width]);
    }

    /**
     * @param int|null $height
     *
     * @return ButtonScene
     */
    public function setHeight(?int $height = null)
    {
        return $this->appendArgs(['height' => $height]);
    }

    /**
     * @param int|null $width
     * @param int|null $height
     *
     * @return ButtonScene
     */
    public function setWidthHeight(?int $width = null, ?int $height = null)
    {
        return $this->setWidth($width)->setHeight($height);
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
     * @param array $fill
     *
     * @return ButtonScene
     */
    public function setFill(array $fill)
    {
        return $this->appendArgs(['fill' => $fill]);
    }

    /**
     * @param bool $closeEasy
     *
     * @return ButtonScene
     */
    public function setShowIframeCloseEasy(bool $closeEasy = true)
    {
        return $this->appendArgs(
            [
                'closable'     => $closeEasy ? false : true,
                'keyboard'     => $closeEasy ? true : false,
                'maskClosable' => $closeEasy ? true : false,
            ]
        );
    }

    /**
     * @return ButtonScene
     */
    public function setShowIframeTitleCenter()
    {
        return $this->appendArgs(['class' => 'bsw-modal-title-center']);
    }

    /**
     * @param string $function
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function setShowIframeWhenDone(string $function, array $params = [])
    {
        return $this->appendArgs(['afterShow' => $function, 'afterShowArgs' => $params]);
    }

    /**
     * @param bool $closeable
     *
     * @return ButtonScene
     */
    public function setCloseable(bool $closeable = true)
    {
        return $this->appendArgs(['closable' => $closeable]);
    }

    /**
     * @param string $text
     *
     * @return ButtonScene
     */
    public function setOkText(string $text)
    {
        return $this->appendArgs(['okText' => $text]);
    }

    /**
     * @param string $text
     *
     * @return ButtonScene
     */
    public function setCancelText(string $text)
    {
        return $this->appendArgs(['cancelText' => $text]);
    }

    /**
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function setPlacement(string $placement = Abs::POS_RIGHT)
    {
        return $this->appendArgs(['placement' => $placement]);
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
     * @return  ButtonScene
     */
    public function setContentDrawer(string $content)
    {
        return $this->setClick('showDrawer')->appendArgs(['content' => $content]);
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
     * @return  ButtonScene
     */
    public function setContentDrawerNoTitle(string $content)
    {
        return $this->setContentDrawer($content)->setNoTitle();
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
     * @param string $placement
     *
     * @return  ButtonScene
     */
    public function setRouteDrawer(string $route, string $placement = Abs::POS_LEFT)
    {
        return $this->setRouteModal($route)->setShowIframeShape(Abs::SHAPE_DRAWER)->setPlacement($placement);
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
     * @return  ButtonScene
     */
    public function setRouteDrawerNoTitle(string $route)
    {
        return $this->setRouteDrawer($route)->setNoTitle();
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
    public function setRouteDrawerAutoTitle(string $route)
    {
        return $this->setRouteDrawer($route)->setAutoTitle();
    }

    /**
     * @param bool $want
     *
     * @return ButtonScene
     */
    public function setParentWindow(bool $want = true)
    {
        return $this->appendArgs(['iframe' => $want]);
    }

    /**
     * @param bool $close
     *
     * @return ButtonScene
     */
    public function setClosePrevModal(bool $close = false)
    {
        return $this->setParentWindow()->appendArgs(['closePrevModal' => $close]);
    }

    /**
     * @param bool $close
     *
     * @return ButtonScene
     */
    public function setClosePrevDrawer(bool $close = false)
    {
        return $this->setParentWindow()->appendArgs(['closePrevDrawer' => $close]);
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setAjaxRequest(string $route)
    {
        return $this->setRoute($route)->setClick('requestByAjax')->appendArgs(['refresh' => true]);
    }

    /**
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneCharmSortModal(string $route, int $id)
    {
        return $this->setType(Abs::THEME_ELE_PRIMARY_OL)
            ->setIcon('b:icon-pin')
            ->setSize(Abs::SIZE_SMALL)
            ->setShape(Abs::SHAPE_ROUND)
            ->setWidthHeight(Abs::MEDIA_XS, 222)
            ->setRouteModalNoTitle($route)
            ->setId($id);
    }

    /**
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneCharmSortDrawer(string $route, int $id)
    {
        return $this->sceneCharmSortModal($route, $id);
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyModal(string $route)
    {
        return $this->setIcon('a:plus')->setRouteModalAutoTitle($route)->setWidthHeight(Abs::MEDIA_SM, 650);
    }

    /**
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyModal(string $route, int $id)
    {
        return $this->sceneOperateNewlyModal($route)->setIcon('b:icon-qianming')->setId($id);
    }

    /**
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyDraw(string $route, int $id)
    {
        return $this->sceneOperateModifyModal($route, $id)->setShowIframeShape(Abs::SHAPE_DRAWER);
    }

    /**
     * @param string $route
     * @param array  $fill
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyWithFillModal(string $route, array $fill)
    {
        return $this->sceneOperateNewlyModal($route)->setIcon('b:icon-add')->setFill($fill);
    }

    /**
     * @param string $route
     * @param array  $fill
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyWithFillDrawer(string $route, array $fill)
    {
        return $this->sceneOperateNewlyWithFillModal($route, $fill)->setShowIframeShape(Abs::SHAPE_DRAWER);
    }

    /**
     * @param string $route
     * @param array  $fill
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyWithFillModal(string $route, array $fill, int $id)
    {
        return $this->sceneOperateNewlyWithFillModal($route, $fill)->setId($id);
    }

    /**
     * @param string $route
     * @param array  $fill
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyWithFillDrawer(string $route, array $fill, int $id)
    {
        return $this->sceneOperateModifyWithFillModal($route, $fill, $id)->setShowIframeShape(Abs::SHAPE_DRAWER);
    }

    /**
     * @param string $content
     * @param array  $options
     *
     * @return ButtonScene
     */
    public function sceneCharmContentModal(string $content, array $options = [])
    {
        $content = Html::tag(
            'pre',
            $content,
            ['class' => 'bsw-pre bsw-long-text']
        );

        return $this->setContentModal($content)
            ->setSize(Abs::SIZE_SMALL)
            ->setType(Abs::THEME_DEFAULT)
            ->setAutoTitle()
            ->setShowIframeCloseEasy()
            ->appendArgs($options);
    }

    /**
     * @param string $content
     * @param array  $options
     *
     * @return ButtonScene
     */
    public function sceneCharmContentDrawer(string $content, array $options = [])
    {
        return $this->sceneCharmContentModal($content, $options)->setClick('showDrawer');
    }

    /**
     * @param array|string $content
     * @param array        $options
     *
     * @return ButtonScene
     */
    public function sceneCharmJsonModal($content, array $options = [])
    {
        $content = Helper::formatPrintJson($content, 2, ': ');
        $content = Html::cleanHtml($content, true);
        $content = Html::tag('code', $content, ['class' => 'language-json']);

        return $this->sceneCharmContentModal($content, $options)
            ->setShowIframeWhenDone('initHighlightBlock', ['selector' => 'div.ant-modal-body code.language-json'])
            ->setWidth(600)
            ->setNoTitle();
    }

    /**
     * @param array|string $content
     * @param array        $options
     *
     * @return ButtonScene
     */
    public function sceneCharmJsonDrawer($content, array $options = [])
    {
        return $this->sceneCharmJsonModal($content, $options)->setClick('showDrawer');
    }

    /**
     * @param string $route
     * @param int    $id
     * @param string $confirm
     *
     * @return ButtonScene
     */
    public function sceneRemoveByAjax(string $route, int $id, string $confirm = null)
    {
        return $this->setType(Abs::THEME_DANGER)
            ->setIcon('b:icon-delete1')
            ->setId($id)
            ->setAjaxRequest($route)
            ->setConfirm($confirm);
    }
}