<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Args;
use Leon\BswBundle\Module\Form\Entity\Traits\Route;

class Upload extends Number
{
    use Route;
    use Args;

    /**
     * @const string
     */
    const LIST_TYPE_TEXT     = 'text';
    const LIST_TYPE_IMG      = 'picture';
    const LIST_TYPE_IMG_CARD = 'picture-card';

    /**
     * @var string
     */
    protected $accept = '*';

    /**
     * @var string
     */
    protected $listType = self::LIST_TYPE_TEXT;

    /**
     * @var string
     */
    protected $flag = 'file';

    /**
     * @var string
     */
    protected $change = 'uploaderChange';

    /**
     * @var string
     */
    protected $placeholder = 'Click to upload';

    /**
     * @var string
     */
    protected $fileListKey;

    /**
     * @var string
     */
    protected $fileMd5Key;

    /**
     * @var string
     */
    protected $fileSha1Key;

    /**
     * @var string
     */
    protected $url;

    /**
     * @return string
     */
    public function getAccept(): string
    {
        return $this->accept;
    }

    /**
     * @param string $accept
     *
     * @return $this
     */
    public function setAccept(string $accept)
    {
        $this->accept = $accept;

        return $this;
    }

    /**
     * @return string
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * @param string $listType
     *
     * @return $this
     */
    public function setListType(string $listType)
    {
        $this->listType = $listType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFlag(): string
    {
        return $this->flag;
    }

    /**
     * @param string $flag
     *
     * @return $this
     */
    public function setFlag(string $flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return string
     */
    public function getChange(): ?string
    {
        return $this->change;
    }

    /**
     * @param string $change
     *
     * @return $this
     */
    public function setChange(string $change = null)
    {
        $this->change = $change;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileListKey(): string
    {
        return $this->fileListKey;
    }

    /**
     * @param string $fileListKey
     *
     * @return $this
     */
    public function setFileListKey(string $fileListKey)
    {
        $this->fileListKey = $fileListKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileMd5Key(): ?string
    {
        return $this->fileMd5Key;
    }

    /**
     * @param string $fileMd5Key
     *
     * @return $this
     */
    public function setFileMd5Key(string $fileMd5Key)
    {
        $this->fileMd5Key = $fileMd5Key;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileSha1Key(): ?string
    {
        return $this->fileSha1Key;
    }

    /**
     * @param string $fileSha1Key
     *
     * @return $this
     */
    public function setFileSha1Key(string $fileSha1Key)
    {
        $this->fileSha1Key = $fileSha1Key;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }
}