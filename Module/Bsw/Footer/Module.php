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
     * @return bool
     */
    public function allowAjax(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return false;
    }

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
        return 'limbs/footer.html';
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
            $this->method,
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'))
        );

        return $output;
    }
}