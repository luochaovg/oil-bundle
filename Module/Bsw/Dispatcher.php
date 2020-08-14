<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Reflection;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Module\Exception\ModuleException;
use stdClass;

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
     * @param array  $extraArgs
     *
     * @return array
     * @throws
     */
    public function execute(string $moduleClass, array $inputArgs, array $extraArgs = []): array
    {
        if (!Helper::extendClass($moduleClass, Bsw::class)) {
            throw new ModuleException("Class {$moduleClass} should extend " . Bsw::class);
        }

        /**
         * @var Bsw $bsw
         */
        $bsw = new $moduleClass($this->web);
        $inputArgsHandling = array_merge($inputArgs, $extraArgs);

        if (($inputArgsHandling['ajax'] ?? false) && !$bsw->allowAjax()) {
            return [null, null, $inputArgs, []];
        }

        if (($inputArgsHandling['iframe'] ?? false) && !$bsw->allowIframe()) {
            return [null, null, $inputArgs, []];
        }

        /**
         * create input args
         */
        $input = $bsw->input();
        $inputReal = [];

        $cls = get_class($input);
        $ref = new Reflection();

        foreach ($input as $attribute => $value) {
            if (array_key_exists($attribute, $inputArgsHandling)) {
                $input->{$attribute} = $inputArgsHandling[$attribute];
            }
            if ($ref->propertyExistsSelf($cls, $attribute)) {
                $inputReal[$attribute] = $input->{$attribute};
            }
        }

        /**
         * handle output args
         */
        $bsw->initialization($input);
        $output = Helper::entityToArray($bsw->logic());

        /**
         * source
         */
        $this->web->appendSrcCss($bsw->css());
        $this->web->appendSrcJs($bsw->javascript());

        $name = $bsw->name();
        $inputArgs['moduleArgs'][$name]['input'] = $input;
        $inputArgs['moduleArgs'][$name]['output'] = $output;

        $exclude = $bsw->inheritExcludeArgs();
        if ($exclude === false) {
            $inputArgs = array_merge($inputArgs, $output);
        } elseif (is_array($exclude)) {
            $outputHanding = $output;
            Helper::arrayPop($outputHanding, $exclude);
            $inputArgs = array_merge($inputArgs, $outputHanding);
        }

        return [$name, $bsw->twig(), $inputArgs, $output];
    }
}