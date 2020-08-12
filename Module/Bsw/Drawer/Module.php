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
        $output = new Output($this->input);

        $output->placement = $this->input->mobile ? $this->input->placementInMobile : $this->input->placement;
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
            $this->arguments(compact('output'), $this->input->args)
        );

        return $output;
    }
}