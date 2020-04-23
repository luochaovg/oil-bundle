<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Entity\FileSize;

class AttachmentLink extends Tailor
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @return mixed|void
     */
    protected function initial()
    {
        $this->alias = "_tailor_{$this->keyword}";
    }

    /**
     * @param array $query
     *
     * @return array
     */
    public function tailorPreviewQuery(array $query): array
    {
        $query = Helper::merge(
            $query,
            [
                'select' => [
                    empty($query['select']) ? $query['alias'] : null,
                    "{$this->alias}.deep AS {$this->keyword}_deep",
                    "{$this->alias}.filename AS {$this->keyword}_filename",
                    "{$this->alias}.size AS {$this->keyword}_size",
                ],
                'join'   => [
                    "{$this->alias}" => [
                        'entity' => BswAttachment::class,
                        'left'   => ["{$query['alias']}.{$this->fieldCamel}", "{$this->alias}.state"],
                        'right'  => ["{$this->alias}.id", Abs::NORMAL],
                    ],
                ],
            ]
        );

        $query['select'] = array_unique($query['select']);

        return $query;
    }

    /**
     * @param array $previewAnnotationExtra
     * @param array $previewAnnotation
     *
     * @return array
     */
    public function tailorPreviewAnnotation(array $previewAnnotationExtra, array $previewAnnotation): array
    {
        $sort = $previewAnnotation[$this->fieldCamel]['sort'] + .01;
        $previewAnnotationExtra[$this->alias] = [
            'label'  => $this->label,
            'render' => Abs::RENDER_LINK,
            'sort'   => $sort,
            'width'  => 400,
        ];
        $previewAnnotationExtra["{$this->keyword}_size"] = [
            'hook' => FileSize::class,
            'show' => false,
        ];

        return $previewAnnotationExtra;
    }

    /**
     * @param array $current
     * @param array $hooked
     * @param array $original
     *
     * @return array
     */
    public function tailorPreviewBeforeRender(array $current, array $hooked, array $original): array
    {
        foreach ($current as &$item) {

            $item = $this->web->attachmentPreviewHandler(
                $item,
                $this->alias,
                ["{$this->keyword}_deep", "{$this->keyword}_filename"]
            );

            if (!empty($item[$this->alias])) {
                if (!empty($item['md5'])) {
                    $item[$this->alias] .= "?" . $this->md1($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->alias] .= "?" . $this->md1($item['sha1']);
                }
            }

            if (!empty($item[$this->fieldCamel])) {
                $key = "{$this->keyword}_size";
                $item[$this->fieldCamel] = "{$item[$this->fieldCamel]} » {$item[$key]}";
            }
        }

        return $current;
    }
}