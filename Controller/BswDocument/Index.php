<?php

namespace Leon\BswBundle\Controller\BswDocument;

use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Parsedown;

trait Index
{
    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function documentDataGenerator(Arguments $args): array
    {
        $file = $this->getFilePath("{$args->title}.md", 'doc');
        $md = file_get_contents($file);

        $parseMarkdown = new Parsedown();
        $document = $parseMarkdown->text($md);

        return compact('document');
    }

    /**
     * Document index
     *
     * @Route("/bsw/document/{title}", name="app_bsw_document")
     *
     * @param string $title
     *
     * @return Response
     */
    public function document(string $title = 'index'): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $this->appendSrcCssWithKey('markdown', Abs::CSS_MARKDOWN);

        return $this->showEmpty('layout/document.html', ['args' => compact('title')]);
    }
}