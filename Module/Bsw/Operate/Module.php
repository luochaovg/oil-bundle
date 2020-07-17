<?php

namespace Leon\BswBundle\Module\Bsw\Operate;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Form\Entity\Button;
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
    const OPERATES = 'Operates';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'operate';
    }

    /**
     * @return string|null
     * @throws
     */
    public function twig(): ?string
    {
        return 'limbs/operate.html';
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

        $buttonScene = [];
        $choiceScene = [
            Abs::SCENE_IFRAME => new Choice(),
            Abs::SCENE_NORMAL => new Choice(),
        ];

        $nowScene = $this->input->iframe ? Abs::SCENE_IFRAME : Abs::SCENE_NORMAL;
        $buttons = $this->caller($this->method, self::OPERATES, Abs::T_ARRAY, []);
        $size = $this->input->mobile ? $this->input->operatesSizeInMobile : $this->input->operatesSize;

        // buttons handler
        foreach ($buttons as $button) {

            /**
             * @var Button $button
             */
            $buttonCls = Button::class;
            if (!Helper::extendClass($button, $buttonCls, true)) {
                $fn = self::OPERATES;
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
            }

            $button->setSize($size);
            $scene = $button->getScene();
            if ($scene === Abs::SCENE_COMMON) {
                $scene = $nowScene;
            }

            // choice
            if ($selector = $button->getSelector()) {
                $choiceScene[$scene]->setEnable()->setMultiple($selector === Abs::SELECTOR_CHECKBOX);
            }

            // script
            $button->setScript(Html::scriptBuilder($button->getClick(), $button->getArgs()));
            $button->setUrl($this->web->urlSafe($button->getRoute(), $button->getArgs(), 'Operate button'));

            if (!$this->web->routeIsAccess($button->getRouteForAccess())) {
                $button->setDisplay(false);
            }

            $buttonScene[$scene][] = $button;
        }

        $output->choice = $choiceScene[$nowScene] ?? $output->choice;
        $output->buttons = $buttonScene[$nowScene] ?? $output->buttons;

        $output->position = $this->input->position;
        if ($this->input->iframe) {
            $output->position = Abs::POS_BOTTOM;
        }

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