<?php

namespace Leon\BswBundle\Module\Bsw\Data;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const DATA_GENERATOR = 'DataGenerator';     // 数据生成

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
        return 'data';
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

        // items
        $arguments = $this->arguments(
            [
                'condition' => $this->input->condition,
                'query'     => $this->input->query,
            ]
        );

        $result = $this->caller(
            $this->method,
            self::DATA_GENERATOR,
            [Message::class, Error::class, Abs::T_ARRAY],
            [],
            $arguments
        );

        if ($result instanceof Error) {
            return $this->showError($result->tiny());
        } elseif ($result instanceof Message) {
            return $this->showMessage($result);
        } else {
            $output->data = $result;
        }

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