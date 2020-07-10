<?php

namespace Leon\BswBundle\Module\Bsw\Drawer;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @return bool
     */
    public function inheritArgs(): bool
    {
        return false;
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
        return 'drawer';
    }

    /**
     * @return string|null
     * @throws
     */
    public function twig(): ?string
    {
        return 'limbs/drawer.html';
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
     */
    public function logic(): ArgsOutput
    {
        $output = new Output();

        $output->title = $this->input->title;
        $output->width = $this->input->width;
        $output->height = $this->input->height;
        $output->placement = $this->input->placement;
        $output->wrapClassName = $this->input->wrapClassName;
        $output->keyboard = $this->input->keyboard;
        $output->mask = $this->input->mask;
        $output->maskClosable = $this->input->maskClosable;
        $output->okText = $this->input->okText;
        $output->okShow = $this->input->okShow;
        $output->cancelText = $this->input->cancelText;
        $output->cancelShow = $this->input->cancelShow;
        $output->okType = $this->input->okType;
        $output->zIndex = $this->input->zIndex;
        $output->closable = $this->input->closable;

        $output->maskStyleJson = Helper::jsonStringify($this->input->maskStyle, '{}', JSON_FORCE_OBJECT);
        $output->wrapStyleJson = Helper::jsonStringify($this->input->wrapStyle, '{}', JSON_FORCE_OBJECT);
        $output->drawerStyleJson = Helper::jsonStringify($this->input->drawerStyle, '{}', JSON_FORCE_OBJECT);
        $output->headerStyleJson = Helper::jsonStringify($this->input->headerStyle, '{}', JSON_FORCE_OBJECT);
        $output->bodyStyleJson = Helper::jsonStringify($this->input->bodyStyle, '{}', JSON_FORCE_OBJECT);

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