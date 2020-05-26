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
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Exception;

/**
 * @property LoggerInterface $logger
 */
trait Upload
{
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

        $upload = $_FILES[$args->file_flag] ?? [];
        $options = $this->uploadOptionByFlag($args->file_flag);

        /**
         * @var UploadItem $file
         */
        $file = $this->uploadCore($upload, $options);
        $sets = [
            'attachment_id'   => $file->id,
            'attachment_url'  => $file->url,
            'attachment_md5'  => $file->md5,
            'attachment_sha1' => $file->sha1,
            'frontend_args'   => (array)$args,
        ];

        if ($href = $file->href ?? null) {
            return $this->responseMessageWithAjax(
                Response::HTTP_OK,
                'File upload done, download {{ url }}',
                $href,
                ['{{ url }}' => $file->url],
                Abs::TAG_CLASSIFY_SUCCESS,
                Abs::TAG_TYPE_CONFIRM,
                $sets,
                Abs::TIME_MINUTE
            );
        }

        return $this->okayAjax($sets, 'File upload done');
    }
}