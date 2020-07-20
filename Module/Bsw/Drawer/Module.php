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
        $output->placement = $this->input->mobile ? $this->input->placementInMobile : $this->input->placement;
        $output->wrapClsName = $this->input->wrapClsName;
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

        $output->maskStyleJson = Helper::jsonFlexible($this->input->maskStyle);
        $output->wrapStyleJson = Helper::jsonFlexible($this->input->wrapStyle);
        $output->drawerStyleJson = Helper::jsonFlexible($this->input->drawerStyle);
        $output->headerStyleJson = Helper::jsonFlexible($this->input->headerStyle);
        $output->bodyStyleJson = Helper::jsonFlexible($this->input->bodyStyle);

        $output = $this->caller(
            $this->method(),
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}