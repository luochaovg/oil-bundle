<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;

class AttachmentFile extends Tailor
{
    /**
     * @return mixed|void
     */
    protected function initial()
    {
        $this->web->appendSrcJs(Abs::JS_FANCY_BOX);
        $this->web->appendSrcCss(Abs::CSS_FANCY_BOX);

        if (!is_array($this->field) || count($this->field) !== 2) {
            $this->field = ['deep', 'filename'];
        }

        $this->fieldCamel = end($this->field);
        $this->label = "_tailor_{$this->fieldCamel}";
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
        $annotationExtra[$this->label] = [
            'label'  => 'Url',
            'render' => Abs::RENDER_LINK,
            'sort'   => $sort,
            'width'  => 400,
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
                $this->label,
                $this->field,
                false
            );

            if (!empty($item[$this->label])) {
                if (!empty($item['md5'])) {
                    $item[$this->label] .= "?" . $this->md1($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->label] .= "?" . $this->md1($item['sha1']);
                }
            }
        }

        return $current;
    }
}