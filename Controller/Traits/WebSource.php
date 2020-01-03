<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property AbstractController  $container
 * @property TranslatorInterface $translator
 */
trait WebSource
{
    /**
     * @var array
     */
    protected $srcExtraPrefixMap = [];

    /**
     * @var array
     * @license
     *   split : for project
     *   split ; for bsw
     */
    protected $srcPrefixMap = [

        // for project
        'odd:' => [
            'tpl' => '/%s',
            'var' => ['src'],
        ],
        'diy:' => [
            'tpl' => '/dist/%s%s%s',
            'var' => ['dist', 'src', 'version'],
        ],
        'npm:' => [
            'tpl' => '/node_modules/%s',
            'var' => ['src'],
        ],

        // for bsw
        'odd;' => [
            'tpl' => '/bundles/leonbsw/%s',
            'var' => ['src'],
        ],
        'diy;' => [
            'tpl' => '/bundles/leonbsw/dist/%s%s%s',
            'var' => ['dist', 'src', 'version'],
        ],
        'npm;' => [
            'tpl' => '/bundles/leonbsw/node_modules/%s',
            'var' => ['src'],
        ],
    ];

    /**
     * @var array
     */
    protected $mapCdnSrcCss = [];

    /**
     * @var array
     */
    protected $initialSrcCss = [
        'ant-d'   => Abs::CSS_ANT_D,
        'animate' => Abs::CSS_ANIMATE,
    ];

    /**
     * @var array
     */
    protected $currentSrcCss = [];

    /**
     * @var array
     */
    protected $positionSrcCss = [
        'ant-d'   => Abs::POS_TOP,
        'animate' => Abs::POS_TOP,
    ];

    /**
     * @var array
     */
    protected $mapCdnSrcJs = [];

    /**
     * @var array
     */
    protected $initialSrcJs = [
        'jquery' => Abs::JS_JQUERY,
        'moment' => Abs::JS_MOMENT,
        'vue'    => Abs::JS_VUE_MIN,
        'ant-d'  => Abs::JS_ANT_D_MIN,
        'app'    => Abs::JS_FOUNDATION,
    ];

    /**
     * @var array
     */
    protected $currentSrcJs = [];

    /**
     * @var array
     */
    protected $positionSrcJs = [
        'jquery' => Abs::POS_TOP,
        'moment' => Abs::POS_TOP,
        'vue'    => Abs::POS_TOP,
        'ant-d'  => Abs::POS_TOP,
        'app'    => Abs::POS_BOTTOM,
    ];

    /**
     * Source handler
     *
     * @return array
     */
    public function source(): array
    {
        [$controller, $method] = $this->getMCM('-');

        $default = "diy:{$controller}/{$method}";
        $version = $this->debug ? mt_rand() : $this->parameter('version');

        $source = [];
        foreach ([Abs::SRC_CSS, Abs::SRC_JS] as $suffix) {

            $type = ucfirst($suffix);
            $name = "currentSrc{$type}";
            $position = "positionSrc{$type}";

            $this->{$name} = array_merge($this->{"initialSrc{$type}"}, $this->{$name});
            $this->{$name} = array_unique($this->{$name});
            $this->{$name} = array_filter($this->{$name});

            foreach ($this->{$name} as $key => &$src) {
                $posKey = is_numeric($key) ? $src : $key;
                $src = ($src === true) ? $default : $src;
                $src = $this->perfectSourceUrl($key, $src, $suffix, $version);

                $pos = $this->{$position}[$posKey] ?? Abs::POS_TOP;
                $source[$suffix][$pos][] = $src;
            }
        }

        return $source;
    }

    /**
     * Perfect source url
     *
     * @param mixed  $cdnKey
     * @param string $src
     * @param string $suffix
     * @param string $version
     *
     * @return string
     */
    protected function perfectSourceUrl(string $cdnKey, string $src, string $suffix, ?string $version)
    {
        if (!Helper::strEndWith($src, ".{$suffix}")) {
            $src = "{$src}.{$suffix}";
        }

        if (Helper::isUrlAlready($src)) {
            return $src;
        }

        if (empty($cdnKey) || is_numeric($cdnKey)) {
            $cdnKey = $src;
        }

        $cdn = "mapCdnSrc" . ucfirst($suffix);
        if (isset($this->{$cdn}[$cdnKey])) {
            return $this->{$cdn}[$cdnKey];
        }

        return $this->caching(
            function () use ($src, $suffix, $version) {

                $dist = $this->debug ? "src-{$suffix}/" : "{$suffix}/";
                if ($version) {
                    $version = "?version={$version}";
                }

                $len = 4;
                $flag = substr($src, 0, $len);
                $prefixMap = array_merge($this->srcPrefixMap, $this->srcExtraPrefixMap);

                if ($prefixMap[$flag] ?? null) {
                    $flag = $prefixMap[$flag];
                    $src = substr($src, $len);
                } else {
                    $flag = $prefixMap['odd:'];
                }

                $variables = [];
                foreach ($flag['var'] as $var) {
                    $variables[] = $$var;
                }

                return sprintf($flag['tpl'], ...$variables);
            }
        );
    }

    /**
     * Append css to stack with non-key
     *
     * @param array|string $css
     * @param string       $position
     *
     * @return void
     */
    public function appendSrcCss($css, string $position = Abs::POS_TOP)
    {
        $css = (array)$css;

        $position = Helper::arrayValuesSetTo($css, $position, true);
        $this->positionSrcCss = array_merge($position, $this->positionSrcCss);

        $this->currentSrcCss = array_merge($this->currentSrcCss, $css);
    }

    /**
     * Append css to stack with key
     *
     * @param string      $key
     * @param string|bool $css
     * @param string      $position
     *
     * @return void
     */
    public function appendSrcCssWithKey(string $key, $css, string $position = Abs::POS_TOP)
    {
        $this->currentSrcCss[$key] = $css;
        if (!isset($this->positionSrcCss[$key])) {
            $this->positionSrcCss[$key] = $position;
        }
    }

    /**
     * Append js to stack with non-key
     *
     * @param array|string $js
     * @param string       $position
     *
     * @return void
     */
    public function appendSrcJs($js, string $position = Abs::POS_BOTTOM)
    {
        $js = (array)$js;

        $position = Helper::arrayValuesSetTo($js, $position, true);
        $this->positionSrcJs = array_merge($position, $this->positionSrcJs);

        $this->currentSrcJs = array_merge($this->currentSrcJs, $js);
    }

    /**
     * Append js to stack with key
     *
     * @param string      $key
     * @param string|bool $js
     * @param string      $position
     *
     * @return void
     */
    public function appendSrcJsWithKey(string $key, $js, string $position = Abs::POS_BOTTOM)
    {
        $this->currentSrcJs[$key] = $js;
        if (!isset($this->positionSrcJs[$key])) {
            $this->positionSrcJs[$key] = $position;
        }
    }

    /**
     * Source for current page with non-key
     *
     * @param array|string|bool $value
     * @param string            $key
     * @param string            $position
     *
     * @return void
     */
    public function currentSrc($value, ?string $key = null, string $position = Abs::POS_BOTTOM)
    {
        if (is_bool($value)) {
            $this->appendSrcCSS($value, $position);
            $this->appendSrcJs($value, $position);

            return;
        }

        if (is_string($value) && $key) {
            $this->appendSrcCssWithKey($key, $value, $position);
            $this->appendSrcJsWithKey($key, $value, $position);

            return;
        }

        $this->appendSrcCss($value, $position);
        $this->appendSrcJs($value, $position);
    }
}