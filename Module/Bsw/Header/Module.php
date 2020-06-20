<?php

namespace Leon\BswBundle\Module\Bsw\Header;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Bsw\Header\Entity\Setting;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const SETTING = 'Setting';
    const LINKS   = 'Links';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'header';
    }

    /**
     * @return string|null
     * @throws
     */
    public function twig(): ?string
    {
        return 'limbs/header.html';
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
        return ['diy;module/scaffold.js'];
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

        // Setting
        $setting = $this->caller($this->method(), self::SETTING, Abs::T_ARRAY, []);
        $method = $this->method() . self::SETTING;

        foreach ($setting as $item) {
            Helper::objectInstanceOf($item, Setting::class, "Method {$method}():array items");
            /**
             * @var Setting $item
             */
            $item->setScript(Html::scriptBuilder($item->getClick(), $item->getArgs()));
            $item->setUrl($this->web->urlSafe($item->getRoute(), $item->getArgs(), 'Header setting'));
            array_push($output->setting, $item);
        }

        // Links
        $links = $this->caller($this->method(), self::LINKS, Abs::T_ARRAY, []);
        $method = $this->method() . self::SETTING;

        foreach ($links as $item) {
            Helper::objectInstanceOf($item, Links::class, "Method {$method}():array items");
            /**
             * @var Links $item
             */
            $item->setScript(Html::scriptBuilder($item->getClick(), $item->getArgs()));
            $item->setUrl($this->web->urlSafe($item->getRoute(), $item->getArgs(), 'Header links'));
            array_push($output->links, $item);
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