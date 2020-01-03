<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Entity\FileSize;

class AttachmentImage extends Tailor
{
    /**
     * @var string
     */
    private $table;

    /**
     * @return mixed|void
     */
    protected function initial()
    {
        $this->web->appendSrcJs([Abs::JS_FANCY_BOX, Abs::JS_LAZY_LOAD]);
        $this->web->appendSrcCss(Abs::CSS_FANCY_BOX);

        $this->table = "_tailor_{$this->keyword}";
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
                    "{$this->table}.deep AS {$this->keyword}_deep",
                    "{$this->table}.filename AS {$this->keyword}_filename",
                    "{$this->table}.size AS {$this->keyword}_size",
                ],
                'join'   => [
                    "{$this->table}" => [
                        'entity' => BswAttachment::class,
                        'left'   => ["{$query['alias']}.{$this->fieldCamel}", "{$this->table}.state"],
                        'right'  => ["{$this->table}.id", Abs::NORMAL],
                    ],
                ],
            ]
        );

        $query['select'] = array_unique($query['select']);

        return $query;
    }

    /**
     * @param array $annotationExtra
     * @param array $annotation
     *
     * @return array
     */
    public function tailorPreviewAnnotation(array $annotationExtra, array $annotation): array
    {
        $sort = $annotation[$this->fieldCamel]['sort'] + .01;
        $annotationExtra[$this->table] = [
            'label'  => $this->label,
            'render' => Abs::RENDER_IMAGE,
            'sort'   => $sort,
            'width'  => 200,
            'align'  => 'center',
        ];
        $annotationExtra["{$this->keyword}_size"] = [
            'hook' => FileSize::class,
            'show' => false,
        ];

        return $annotationExtra;
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
                $this->table,
                ["{$this->keyword}_deep", "{$this->keyword}_filename"]
            );

            if (!empty($item[$this->table])) {
                if (!empty($item['md5'])) {
                    $item[$this->table] .= "?" . md5($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->table] .= "?" . md5($item['sha1']);
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