<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Reflection;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Module\Exception\ModuleException;
use ReflectionClass;

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
     * @param array  $acmeArgs
     * @param array  $extraArgs
     *
     * @return array
     * @throws
     */
    public function execute(string $moduleClass, array $acmeArgs, array $extraArgs = []): array
    {
        if (!Helper::extendClass($moduleClass, Bsw::class)) {
            throw new ModuleException("Class {$moduleClass} should extend " . Bsw::class);
        }

        /**
         * @var Bsw $bsw
         */
        $bsw = new $moduleClass($this->web);
        $input = $bsw->input();
        $exclude = $bsw->inheritExcludeArgs();

        if ($exclude === true) {
            $ref = new ReflectionClass($input);
            $exclude = [];
            foreach ($ref->getProperties() as $property) {
                if ($property->class === $ref->name) {
                    $exclude[] = $property->name;
                }
            }
        }

        if (is_array($exclude)) {
            Helper::arrayPop($acmeArgs, $exclude);
        }

        $inputArgsHandling = array_merge($acmeArgs, $extraArgs);
        if (($inputArgsHandling['ajax'] ?? false) && !$bsw->allowAjax()) {
            return [null, null, $acmeArgs, []];
        }

        if (($inputArgsHandling['iframe'] ?? false) && !$bsw->allowIframe()) {
            return [null, null, $acmeArgs, []];
        }

        /**
         * create input args
         */
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
        $acmeArgs['moduleArgs'][$name]['input'] = $input;
        $acmeArgs['moduleArgs'][$name]['output'] = $output;
        $acmeArgs = array_merge($acmeArgs, $output);

        return [$name, $bsw->twig(), $acmeArgs, $output];
    }
}