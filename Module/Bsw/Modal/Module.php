<?php

namespace Leon\BswBundle\Module\Bsw\Modal;

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
     * @return string
     */
    public function name(): string
    {
        return 'modal';
    }

    /**
     * @return string|null
     * @throws
     */
    public function twig(): ?string
    {
        return 'limbs/modal.html';
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
        $output->footer = $this->input->footer;
        $output->wrapClassName = $this->input->wrapClassName;
        $output->keyboard = $this->input->keyboard;
        $output->mask = $this->input->mask;
        $output->maskClosable = $this->input->maskClosable;
        $output->okText = $this->input->okText;
        $output->cancelText = $this->input->cancelText;
        $output->okType = $this->input->okType;
        $output->zIndex = $this->input->zIndex;
        $output->closable = $this->input->closable;
        $output->bodyStyle = Helper::jsonStringify($this->input->bodyStyle, '{}', JSON_FORCE_OBJECT);
        $output->maskStyle = Helper::jsonStringify($this->input->maskStyle, '{}', JSON_FORCE_OBJECT);
        $output->dialogStyle = Helper::jsonStringify($this->input->dialogStyle, '{}', JSON_FORCE_OBJECT);
        $output->okCallback = $this->input->okCallback;
        $output->cancelCallback = $this->input->cancelCallback;
        $output->afterCloseCallback = $this->input->afterCloseCallback;

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