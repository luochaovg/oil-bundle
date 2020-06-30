<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Component\Upload as Uploader;
use Leon\BswBundle\Component\UploadItem;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorUpload;
use Leon\BswBundle\Repository\BswAttachmentRepository;
use OSS\Core\OssException;
use Monolog\Logger;
use OSS\OssClient;
use Exception;

/**
 * @property Logger $logger
 */
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
     * @var array upload archive
     */
    public static $archiveMap = [
        'application/x-bzip'           => 'bz',
        'application/x-bzip2'          => 'bz2',
        'application/x-rar-compressed' => 'rar',
        'application/x-tar'            => 'tar',
        'application/zip'              => 'zip',
        'application/x-7z-compressed'  => '7z',
    ];

    /**
     * @var array upload excel
     */
    public static $excelMap = [
        'application/vnd.ms-excel'                                          => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    ];

    /**
     * @var array upload csv
     */
    public static $csvMap = [
        'text/plain' => 'csv',
        'text/csv'   => 'csv',
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
        'image/bmp'     => 'bmp',
        'image/webp'    => 'webp',
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

    /**
     * Get upload option with flag
     *
     * @param string $flag
     * @param bool   $manual
     *
     * @return array
     */
    public function uploadOptionByFlag(string $flag, bool $manual = false): array
    {
        $default = [
            'max_size'     => 128 * Abs::HEX_SIZE,
            'suffix'       => [],
            'mime'         => [],
            'pic_sizes'    => [[10, 'max'], [10, 'max']],
            'save_replace' => false,
            'root_path'    => $this->parameter('file'),
            'manual'       => $manual,
            'save_name_fn' => ['uniqid'],
            'save_path_fn' => function () use ($flag) {
                return $flag;
            },
        ];

        return $this->dispatchMethod(Abs::FN_UPLOAD_OPTIONS, $default, [$flag, $default]);
    }

    /**
     * @param UploadItem $file
     *
     * @return UploadItem
     * @throws
     */
    public function ossUpload(UploadItem $file): UploadItem
    {
        $ossKey = $this->parameterInOrderByEmpty(['ali_oss_key', 'ali_key']);
        $ossSecret = $this->parameterInOrderByEmpty(['ali_oss_secret', 'ali_secret']);

        if (!$this->parameter('upload_to_oss') || empty($ossKey) || $ossSecret) {
            return $file;
        }

        try {

            $fileName = "{$file->savePath}/{$file->saveName}";

            $ossClient = new OssClient($ossKey, $ossSecret, $this->parameter('ali_oss_endpoint'));
            $ossClient->setConnectTimeout($this->cnf->curl_timeout_second * 20);
            $ossClient->setTimeout($this->cnf->curl_timeout_second * 20);

            $ossClient->uploadFile(
                $this->parameter('ali_oss_bucket'),
                $fileName,
                $file->file
            );

        } catch (OssException $e) {

            $this->logger->error("Ali oss upload error: {$e->getMessage()}");

            return $file;
        }

        // remove local file
        if ($this->cnf->rm_local_file_when_oss ?? false) {
            @unlink($file->file);
        }

        return $file;
    }

    /**
     * Upload core
     *
     * @param array $file
     * @param array $options
     * @param int   $platform
     *
     * @return object
     * @throws
     */
    public function uploadCore(array $file, array $options, int $platform = 2)
    {
        // upload
        try {
            $file = current((new Uploader($options))->upload([$file]));
        } catch (Exception $e) {
            if ($options['manual']) {
                throw new Exception($e->getMessage());
            } else {
                return $this->failedAjax(new ErrorUpload(), $e->getMessage());
            }
        }

        $userId = $this->usr('usr_uid') ?? 0;

        /**
         * @var BswAttachmentRepository $bswAttachment
         */
        $bswAttachment = $this->repo(BswAttachment::class);
        $exists = $bswAttachment->findOneBy(
            $unique = [
                'sha1'     => $file->sha1,
                'platform' => $platform,
                'userId'   => $userId,
            ]
        );

        if ($exists) {

            // The file already exists
            if ($exists->state !== Abs::NORMAL) {
                $bswAttachment->modify(['id' => $exists->id], ['state' => Abs::NORMAL]);
            }

            $file->savePath = $exists->deep;
            $file->saveName = $exists->filename;
            $file->id = $exists->id;

        } else {

            // The file is new and upload to oss
            $file->id = $bswAttachment->newly(
                [
                    'platform' => $platform,
                    'userId'   => $userId,
                    'sha1'     => $file->sha1,
                    'size'     => $file->size,
                    'deep'     => $file->savePath,
                    'filename' => Html::cleanHtml($file->saveName),
                    'state'    => Abs::NORMAL,
                ]
            );
            $file = $this->ossUpload($file);
        }

        // file url
        $file = $this->attachmentPreviewHandler($file, 'url', ['savePath', 'saveName'], false);
        if (is_callable($options['file_fn'] ?? null)) {
            $file = call_user_func_array($options['file_fn'], [$file]);
        }

        return $file;
    }
}