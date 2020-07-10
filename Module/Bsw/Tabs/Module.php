<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
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
     * @const string
     */
    const TABS_LINKS = 'TabsLinks';

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'tabs';
    }

    /**
     * @return string|null
     */
    public function twig(): ?string
    {
        return 'limbs/tabs.html';
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

        // links
        $links = $this->caller($this->method, self::TABS_LINKS, Abs::T_ARRAY, []);
        foreach ($links as $item) {
            $fn = $this->method . self::TABS_LINKS;
            Helper::objectInstanceOf(
                $item,
                Links::class,
                "{$this->class}::{$fn}(): array returned array'items"
            );

            /**
             * @var Links $item
             */
            $item->setScript(Html::scriptBuilder($item->getClick(), $item->getArgs()));
            $item->setUrl($this->web->urlSafe($item->getRoute(), $item->getArgs(), 'Tabs links'));
            array_push($output->links, $item);
        }

        $output->fit = $this->input->fit;
        $output->size = $this->input->size;

        $output = $this->caller(
            $this->method,
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}