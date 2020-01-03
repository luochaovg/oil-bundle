<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Exception\UploadException;
use Exception;

class Upload
{
    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var array
     */
    protected $nameNoChar = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];

    /**
     * @var float|int
     */
    protected $maxSize = 1024 * 8;

    /**
     * @var array
     */
    protected $suffix = [];

    /**
     * @var array
     */
    protected $noSuffix = ['php'];

    /**
     * @var array
     */
    protected $mime = [];

    /**
     * @var array
     */
    protected $noMime = [];

    /**
     * @var array
     */
    protected $picSizes = [[10, 'max'], [10, 'max']];

    /**
     * @var array
     */
    protected $savePathFn = [];

    /**
     * @var array
     */
    protected $saveNameFn = ['uniqid'];

    /**
     * @var bool
     */
    protected $saveReplace = false;

    /**
     * @var array
     */
    protected $imageSuffix = ['gif', 'jpg', 'jpeg', 'png'];

    /**
     * Upload constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $item => $value) {
            $item = Helper::underToCamel($item);
            if (!property_exists($this, $item)) {
                continue;
            }

            $this->{$item} = $value;

            $stringToArray = ['suffix', 'noSuffix', 'mime', 'noMime'];
            if (in_array($item, $stringToArray) && is_string($this->{$item})) {
                $this->{$item} = Helper::stringToArray($this->{$item});
            }
        }
    }

    /**
     * Upload File
     *
     * @param array $files
     *
     * @return UploadItem[]
     * @throws
     */
    public function upload(array $files): array
    {
        // no file
        if (empty($files)) {
            throw new UploadException('No file upload');
        }

        // check root path
        $this->checkRootPath($this->rootPath);

        // check file one by one
        if (function_exists('finfo_open')) {
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        }

        $result = [];

        foreach ($files as $key => $item) {

            if (!empty($item['error'])) {
                throw new UploadException('Unknown upload error', $item['error']);
            }

            $file = new UploadItem($item['tmp_name'], $item['name'], $item['key'] ?? $key, $item['size']);

            // get suffix by extend for adobe.flash upload
            if (isset($fileInfo)) {
                $file->type = strtolower(finfo_file($fileInfo, $file->tmpName));
            }

            // check file
            $this->check($file);

            // create sub directory
            $file->savePath = $this->getSavePath();

            // create save name
            $file->saveName = $this->getSaveName($file);

            // check image
            if (in_array($file->suffix, $this->imageSuffix)) {

                // check sizes
                if ($this->picSizes && !$this->checkSizes($file->tmpName)) {
                    throw new UploadException('Image sizes error');
                }

                // check core
                $imgInfo = getimagesize($file->tmpName);
                if (empty($imgInfo) || ('gif' === $file->suffix && empty($imgInfo['bits']))) {
                    throw new UploadException('Image illegal');
                }

                // get width and height
                [$file->width, $file->height] = $imgInfo;
            }

            // save file
            if ($file->file = $this->save($file, $this->saveReplace)) {

                $file->fileName = Helper::joinString(DIRECTORY_SEPARATOR, $file->savePath, $file->saveName);
                $file->md5 = md5_file($file->file);
                $file->sha1 = sha1_file($file->file);

                $result[$key] = $file;
            }
        }

        isset($fileInfo) && finfo_close($fileInfo);

        return $result;
    }

    /**
     * Check The File
     *
     * @param UploadItem $file
     *
     * @return bool
     * @throws
     */
    private function check(UploadItem $file): bool
    {
        if (empty($file->name)) {
            throw new UploadException('Unknown upload error');
        }

        if (!empty($this->nameNoChar)) {
            foreach ($this->nameNoChar as $char) {
                if (strpos($file->name, $char) === false) {
                    continue;
                }
                throw new UploadException('File name illegal');
            }
        }

        if (!is_uploaded_file($file->tmpName)) {
            throw new UploadException('File illegal');
        }

        // check size
        if (!$this->checkSize($file->size)) {
            throw new UploadException('File size error');
        }

        // check suffix
        if (!empty($this->suffix)) {
            if (!$this->checkSuffix($file->suffix, $this->suffix)) {
                throw new UploadException('File suffix not allow');
            }
        } else {
            if (!empty($this->noSuffix) && $this->checkSuffix($file->suffix, $this->noSuffix)) {
                throw new UploadException('File suffix not allow');
            }
        }

        // check mime
        // all file mime is application/octet-stream by adobe.flash upload
        if (!empty($this->mime)) {
            if (isset($file->type) && !$this->checkMime($file->type, $this->mime)) {
                throw new UploadException('File mime not allow');
            }
        } else {
            if (!empty($this->noMime) && $this->checkSuffix($file->suffix, $this->noMime)) {
                throw new UploadException('File mime not allow');
            }
        }

        return true;
    }

    /**
     * Check size
     *
     * @param int $size
     *
     * @return bool
     */
    private function checkSize(int $size): bool
    {
        $size /= 1024; // B to KB

        return ($size <= $this->maxSize) || (0 == $this->maxSize);
    }

    /**
     * Check mime
     *
     * @param string $mime
     * @param array  $allow
     *
     * @return bool
     */
    private function checkMime(string $mime, array $allow): bool
    {
        return empty($allow) ? true : in_array($mime, $allow);
    }

    /**
     * Check suffix
     *
     * @param string $suffix
     * @param array  $allow
     *
     * @return bool
     */
    private function checkSuffix(string $suffix, array $allow): bool
    {
        return empty($allow) ? true : in_array($suffix, $allow);
    }

    /**
     * Check sizes
     *
     * @param string $filePath
     *
     * @return bool
     * @throws
     */
    private function checkSizes(string $filePath): bool
    {
        [$width, $height] = getimagesize($filePath);

        try {

            [$ruleSizes['width'], $ruleSizes['height']] = $this->picSizes;

            /**
             * Check pic width and height
             *
             * @param array  $ruleSizes
             * @param string $type
             *
             * @return bool
             */
            $checkWidthAndHeight = function ($ruleSizes, $type) use ($width, $height) {
                if (is_array($ruleSizes[$type])) {
                    [$min, $max] = $ruleSizes[$type];
                    if (strtolower($max) === 'max') {
                        $max = ${$type};
                    }
                    if (${$type} < intval($min) || ${$type} > intval($max)) {
                        return false;
                    }
                } else {
                    if (intval($ruleSizes[$type]) != ${$type}) {
                        return false;
                    }
                }

                return true;
            };

            if (!$checkWidthAndHeight($ruleSizes, 'width')) {
                return false;
            }

            if (!$checkWidthAndHeight($ruleSizes, 'height')) {
                return false;
            }

        } catch (Exception $e) {
            throw new UploadException('Picture sizes format error');
        }

        return true;
    }

    /**
     * Get save name
     *
     * @param UploadItem $file
     *
     * @return string
     * @throws
     */
    private function getSaveName(UploadItem $file): string
    {
        if (empty($this->saveNameFn)) {
            return $file->name;
        }

        $saveName = $this->createName($this->saveNameFn);

        if (empty($file->suffix)) {
            return $saveName;
        }

        return "{$saveName}.{$file->suffix}";
    }

    /**
     * Get save directory name
     *
     * @return string
     * @throws
     */
    private function getSavePath(): ?string
    {
        $subPath = null;

        if (empty($this->savePathFn)) {
            return $subPath;
        }

        $subPath = $this->createName($this->savePathFn);
        $fullPath = Helper::joinString(DIRECTORY_SEPARATOR, $this->rootPath, $subPath);
        if (!empty($subPath) && !is_dir($fullPath) && !mkdir($fullPath, 0777, true)) {
            throw new UploadException('Create directory fail');
        }

        return $subPath;
    }

    /**
     * Create name by rule
     *
     * @param array|callable $rule
     *
     * @return string
     */
    private function createName($rule): string
    {
        $rule = (array)$rule + [1 => []];
        [$fn, $params] = $rule;

        if (is_string($fn) && !function_exists($fn)) {
            $fn = [$this, $fn];
        }

        return call_user_func_array($fn, $params);
    }

    /**
     * Check directory
     *
     * @param string $rootPath
     *
     * @return string
     * @throws
     */
    private function checkRootPath(string $rootPath = null): string
    {
        if (empty($rootPath)) {
            throw new UploadException('Root path is required');
        }

        if (!is_dir($rootPath) && !mkdir($rootPath, 0777, true)) {
            throw new UploadException('Create directory fail');
        }

        return $this->rootPath = realpath($rootPath) . DIRECTORY_SEPARATOR;
    }

    /**
     * Save file
     *
     * @param UploadItem $file
     * @param bool       $replace
     *
     * @return string
     * @throws
     */
    private function save(UploadItem $file, bool $replace = false): string
    {
        $fileName = Helper::joinString(DIRECTORY_SEPARATOR, $this->rootPath, $file->savePath, $file->saveName);

        // replace file
        if (!$replace && is_file($fileName)) {
            throw new UploadException('File move fail, file exists');
        }

        // move file
        if (!move_uploaded_file($file->tmpName, $fileName)) {
            throw new UploadException('File move error');
        }

        return $fileName;
    }
}