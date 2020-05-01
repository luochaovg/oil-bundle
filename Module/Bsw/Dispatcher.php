<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Module\Exception\ModuleException;

class Dispatcher
{
    /**
     * @var BswWebController
     */
    protected $web;

    /**
     * Dispatcher constructor.
     *
     * @param BswWebController $web
     */
    public function __construct(BswWebController $web)
    {
        $this->web = $web;
    }

    /**
     * Execute
     *
     * @param string $moduleClass
     * @param array  $inputArgs
     *
     * @return array
     * @throws
     */
    public function execute(string $moduleClass, array $inputArgs): array
    {
        if (!Helper::extendClass($moduleClass, Bsw::class)) {
            throw new ModuleException("Class {$moduleClass} should extend " . Bsw::class);
        }

        /**
         * @var Bsw $bsw
         */
        $bsw = new $moduleClass($this->web);

        if (($inputArgs['ajax'] ?? false) && !$bsw->allowAjax()) {
            return [null, null, [], $inputArgs];
        }

        if (($inputArgs['iframe'] ?? false) && !$bsw->allowIframe()) {
            return [null, null, [], $inputArgs];
        }

        /**
         * create input args
         */
        $input = $bsw->input();
        foreach ($input as $attribute => $value) {
            $input->{$attribute} = $inputArgs[$attribute] ?? $value;
        }

        /**
         * handle output args
         */
        $bsw->initialization($input);
        $output = Helper::entityToArray($bsw->logic());
        $inputArgs = array_merge($output, $inputArgs);

        /**
         * source
         */
        $this->web->appendSrcCss($bsw->css());
        $this->web->appendSrcJs($bsw->javascript());

        /**
         * scalar
         */
        $name = $bsw->name();
        $twig = $bsw->twig();

        return [$name, $twig, $output, $inputArgs];
    }
}