<?php

namespace Leon\BswBundle\Module\Bsw\Chart;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
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

        if ($this->input->itemsHandler) {
            $args = [$output->items, $this->input->condition];
            $output->items = call_user_func_array($this->input->itemsHandler, $args);
        } else {
            $output->items = $this->input->items;
        }

        // source
        $this->web->appendSrcJsWithKey('e-charts', Abs::JS_CHART);

        $themes = array_column($output->items, 'theme');
        $themes = array_unique($themes);
        foreach ($themes as $theme) {
            $theme = strtoupper($theme);
            $theme = str_replace('-', '_', $theme);
            $this->web->appendSrcJs(constant(Abs::class . '::JS_CHART_' . $theme));
        }

        foreach ($this->input->tabsMenu as $item) {

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
            array_push($output->tabsMenu, $item);
        }

        $output = $this->caller(
            $this->method . ucfirst($this->name()),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}