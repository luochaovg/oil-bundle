<?php

namespace Leon\BswBundle\Module\Bsw\Welcome;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const WELCOME = 'Welcome';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'welcome';
    }

    /**
     * @return string|null
     */
    public function twig(): ?string
    {
        return '@LeonBsw/limbs/welcome';
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
        $output->speech = $this->caller($this->method, self::WELCOME, Abs::T_STRING);

        $output = $this->caller(
            $this->method . ucfirst($this->name()),
            self::ARGS_BEFORE_RENDER,
            Output::class,
            $output,
            [$output]
        );

        return $output;
    }
}