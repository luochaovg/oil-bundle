<?php

namespace Leon\BswBundle\Controller\Traits;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property AbstractController  $container
 * @property TranslatorInterface $translator
 */
trait WebSeo
{
    /**
     * @var string
     */
    protected $seoTitle;

    /**
     * @var string
     */
    protected $seoDescription;

    /**
     * @var string
     */
    protected $seoKeywords;

    /**
     * Search engine optimization
     *
     * @return array
     */
    public function seo(): array
    {
        /**
         * @var $request SfRequest
         */
        $request = $this->request();
        $i18n = $request->getLocale();

        /**
         * Trans key
         *
         * @param string $type
         *
         * @return null|string
         */
        $get = function (string $type) use ($i18n) {

            $key = "{$this->route}_{$type}";
            $cnfKey = "{$key}_{$i18n}";

            $message = $this->cnf->{$cnfKey} ?? $this->translator->trans($key, [], 'seo');
            $message = ($message == $key) ? null : $message;
            $appName = $this->cnf->app_name ?? 'UnsetAppName';

            if ($message) {
                switch ($type) {
                    case 't':
                        $message = " - {$message}";
                        break;
                    case 'd':
                    case 'k':
                        $message = ", {$message}";
                        break;
                }
            }

            return "{$appName}{$message}";
        };

        list($this->seoTitle, $this->seoDescription, $this->seoKeywords) = $this->caching(
            function () use (&$get) {
                return [
                    $this->seoTitle ?: $get('t'),
                    $this->seoDescription ?: $get('d'),
                    $this->seoKeywords ?: $get('k'),
                ];
            },
            "seo.{$this->route}"
        );

        return [
            'title'       => $this->seoTitle,
            'description' => $this->seoDescription,
            'keyword'     => $this->seoKeywords,
        ];
    }
}