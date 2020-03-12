<?php

namespace Leon\BswBundle\Twig;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

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
            new TwigFilter('array_map', [Helper::class, 'arrayMap']),
            new TwigFilter('array_map_double', [Helper::class, 'arrayMapDouble']),
            new TwigFilter('icon', [$this, 'icon']),
            new TwigFilter('implode', [$this, 'implode']),
        ];
    }

    /**
     * Get icon html
     *
     * @param string $icon
     * @param bool   $html
     * @param array  $class
     *
     * @return string
     */
    public static function icon(string $icon, bool $html = true, array $class = [])
    {
        $flag = 'a';

        if (strpos($icon, ':') !== false) {
            list($flag, $icon) = explode(':', $icon);
        }

        if (!$html) {
            return $icon;
        }

        return Html::tag(
            "{$flag}-icon",
            null,
            [
                'type'  => $icon,
                'class' => $class,
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
}