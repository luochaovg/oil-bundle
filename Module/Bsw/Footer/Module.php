<?php

namespace Leon\BswBundle\Module\Bsw\Footer;

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
        return 'footer';
    }

    /**
     * @return string|null
     * @throws
     */
    public function twig(): ?string
    {
        return $this->web->twigElection('footer', 'limbs');
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