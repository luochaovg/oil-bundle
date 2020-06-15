<?php

namespace Leon\BswBundle\Module\Bsw\Chart;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const CHART_ITEMS = 'ChartItems';   // 图表数据
    const CHART_MENU  = 'ChartMenu';    // 图表菜单

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'chart';
    }

    /**
     * @return string|null
     */
    public function twig(): ?string
    {
        return null;
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
        return null;
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * @return ArgsOutput
     * @throws
     */
    public function logic(): ArgsOutput
    {
        $output = new Output();

        // menu
        $menu = $this->caller($this->method, self::CHART_MENU, Abs::T_ARRAY, []);
        foreach ($menu as $item) {
            Helper::objectInstanceOf(
                $item,
                Links::class,
                "Argument `tabsMenu` in {$this->class}::{$this->method}() when response"
            );

            /**
             * @var Links $item
             */
            if ($item->isScript()) {
                $item->setUrl($item->getRoute());
            } else {
                $item->setUrl($this->web->urlSafe($item->getRoute(), [], 'Chart tabs links'));
            }
            array_push($output->menu, $item);
        }

        // items
        $arguments = $this->arguments(['condition' => $this->input->condition, 'data' => $this->input->data]);
        $result = $this->caller(
            $this->method,
            self::CHART_ITEMS,
            [Message::class, Error::class, Abs::T_ARRAY],
            [],
            $arguments
        );

        if ($result instanceof Error) {
            return $this->showError($result->tiny());
        } elseif ($result instanceof Message) {
            return $this->showMessage($result);
        } else {
            $output->items = $result;
        }

        // resource
        $this->web->appendSrcJsWithKey('e-charts', Abs::JS_CHART);
        foreach ($output->items as $item) {
            $this->web->appendSrcJs($item->getTheme());
        }

        $output = $this->caller(
            $this->method . Helper::underToCamel($this->name(), false),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}