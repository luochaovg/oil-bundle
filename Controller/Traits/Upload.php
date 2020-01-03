<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;

trait Upload
{
    /**
     * @var array upload document
     */
    public static $docMap = [
        'text/plain'                    => 'txt',
        'text/markdown'                 => 'md',
        'application/pdf'               => 'pdf',
        'application/msword'            => 'doc',
        'application/vnd.ms-powerpoint' => 'ppt',
    ];

    /**
     * @var array upload excel
     */
    public static $excelMap = [
        'application/vnd.ms-excel'                                          => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    ];

    /**
     * @var array upload rar
     */
    public static $rarMap = [
        'application/zip' => 'rar',
    ];

    /**
     * @var array upload pictures
     */
    public static $imgMap = [
        'image/png'     => 'png',
        'image/gif'     => 'gif',
        'image/jpeg'    => 'jpeg',
        'image/jpg'     => 'jpg',
        'image/svg+xml' => 'svg',
    ];

    /**
     * @var array android package
     */
    public static $apkMap = [
        'application/vnd.android.package-archive' => 'apk',
        'application/zip'                         => 'apk',
    ];

    /**
     * @var array ios package
     */
    public static $ipaMap = [
        'application/octet-stream.ipa' => 'ipa',
        'application/x-ios-app'        => 'ipa',
        'application/zip'              => 'ipa',
    ];

    /**
     * Merge maps
     *
     * @param array ...$maps
     *
     * @return array
     */
    public static function mergeMimeMaps(...$maps)
    {
        $mime = $suffix = [];
        foreach ($maps as $item) {
            $mime = array_merge($mime, array_keys($item));

            $_suffix = Helper::stringToArray(implode(',', array_values($item)));
            $suffix = array_merge($suffix, $_suffix);
        }

        return [
            'mime'   => array_filter(array_unique($mime)),
            'suffix' => array_filter(array_unique($suffix)),
        ];
    }
}