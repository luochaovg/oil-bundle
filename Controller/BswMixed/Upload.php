<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\UploadItem;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorUpload;
use Leon\BswBundle\Repository\BswAttachmentRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Component\Upload as Uploader;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Exception;

/**
 * @property LoggerInterface $logger
 */
trait Upload
{
    /**
     * @param UploadItem $file
     *
     * @return array
     * @throws
     */
    protected function persistenceUpload(UploadItem $file): array
    {
        /**
         * @var BswAttachmentRepository $bswAttachment
         */
        $bswAttachment = $this->repo(BswAttachment::class);
        $exists = $bswAttachment->findOneBy(
            $unique = [
                'sha1'     => $file->sha1,
                'platform' => 2,
                'userId'   => $this->usr->{$this->cnf->usr_uid},
            ]
        );

        if ($exists) {

            if ($exists->state !== Abs::NORMAL) {
                $bswAttachment->modify(['id' => $exists->id], ['state' => Abs::NORMAL]);
            }

            $file->savePath = $exists->deep;
            $file->saveName = $exists->filename;
            $file->id = $exists->id;

            return [false, $file];
        }

        $file->id = $bswAttachment->newly(
            [
                'platform' => 2,
                'userId'   => $this->usr->{$this->cnf->usr_uid},
                'sha1'     => $file->sha1,
                'size'     => $file->size,
                'deep'     => $file->savePath,
                'filename' => $file->saveName,
                'state'    => Abs::NORMAL,
            ]
        );

        return [true, $file];
    }

    /**
     * Upload core
     *
     * @param object $args
     *
     * @return Response
     */
    public function uploadCore($args): Response
    {
        $options = $this->uploadOptionByFlag($args->file_flag);

        // upload
        try {
            $file = $_FILES[$args->file_flag] ?? [];
            $file = current((new Uploader($options))->upload([$file]));
        } catch (Exception $e) {
            return $this->failedAjax(new ErrorUpload(), $e->getMessage());
        }

        // persistence attachment
        [$new, $file] = $this->persistenceUpload($file);
        if ($new) {
            $file = $this->ossUpload($file);
        }

        // file url
        $file = $this->attachmentPreviewHandler($file, 'url', ['savePath', 'saveName'], false);

        return $this->okayAjax(
            [
                'attachment_id'   => $file->id,
                'attachment_url'  => $file->url,
                'attachment_md5'  => $file->md5,
                'attachment_sha1' => $file->sha1,
                'frontend_args'   => (array)$args,
            ],
            'File upload done'
        );
    }

    /**
     * File uploader
     *
     * @Route("/upload", name="app_upload")
     * @Access()
     *
     * @I("file_flag")
     *
     * @O("attachment_id")
     * @O("attachment_url")
     * @O("attachment_md5")
     * @O("attachment_sha1")
     * @O("frontend_args")
     *
     * @return Response
     * @throws
     */
    public function uploadAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->uploadCore($args);
    }
}