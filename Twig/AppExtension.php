<?php

namespace Leon\BswBundle\Twig;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use function GuzzleHttp\Psr7\parse_query;

class AppExtension extends AbstractExtension
{
    /**
     * Register filters
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('arrayMap', [Helper::class, 'arrayMap']),
            new TwigFilter('arrayMapDouble', [Helper::class, 'arrayMapDouble']),
            new TwigFilter('icon', [$this, 'icon']),
            new TwigFilter('nativeIcon', [$this, 'nativeIcon']),
            new TwigFilter('implode', [$this, 'implode']),
            new TwigFilter('imageStyle', [$this, 'imageStyle']),
            new TwigFilter('stringify', [Helper::class, 'jsonStringify']),
        ];
    }

    /**
     * Get icon html
     *
     * @param string $icon
     * @param bool   $html
     * @param array  $class
     * @param array  $queryArr
     *
     * @return string|null
     */
    public static function icon(string $icon, bool $html = true, array $class = [], array $queryArr = [])
    {
        $flag = 'a';
        $query = null;

        if (strpos($icon, ':') !== false) {
            [$flag, $icon, $query] = explode(':', $icon) + [2 => null];
        }

        if (!$html) {
            return $icon;
        }

        $query = parse_query($query);
        foreach ($query as &$value) {
            $value = is_null($value) ? true : $value;
        }

        return Html::tag(
            "{$flag}-icon",
            null,
            array_merge(
                $query,
                $queryArr,
                [
                    'type'  => $icon,
                    'class' => $class,
                ]
            )
        );
    }

    /**
     * Get native icon html
     *
     * @param string $icon
     * @param array  $class
     *
     * @return string|null
     */
    public static function nativeIcon(string $icon, array $class = [])
    {
        $flag = 'a';
        if (strpos($icon, ':') !== false) {
            [$flag, $icon] = explode(':', $icon) + [2 => null];
        }

        if ($flag !== 'b') {
            return '';
        }

        array_unshift($class, 'bsw-icon');

        return Html::tag(
            'svg',
            Html::tag('use', null, ['xlink:href' => "#{$icon}"]),
            [
                'class'       => $class,
                'aria-hidden' => true,
            ]
        );
    }

    /**
     * Implode after array_filter
     *
     * @param array  $source
     * @param string $split
     *
     * @return string
     */
    public static function implode(array $source, string $split = null): string
    {
        return implode($split, array_filter($source));
    }

    /**
     * Image style
     *
     * @param array  $config
     * @param string $flag
     * @param bool   $boundary
     *
     * @return string
     */
    public static function imageStyle(array $config, string $flag, bool $boundary = false): string
    {
        $map = [
            'image'    => 'url(%s) !important',
            'repeat'   => '%s !important',
            'color'    => '%s !important',
            'position' => '%s !important',
            'size'     => '%s !important',
        ];

        $attributes = [];

        foreach ($map as $tag => $tpl) {
            $key = Helper::underToCamel("{$flag}_background_{$tag}");
            if (!empty($config[$key])) {
                $_key = "background-{$tag}";
                $val = sprintf($tpl, $config[$key]);
                array_push($attributes, "{$_key}: {$val}");
            }
        }

        $attributes = implode('; ', $attributes);

        return $boundary ? "{ {$attributes} }" : $attributes;
    }
}