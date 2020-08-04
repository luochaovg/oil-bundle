<?php

namespace Leon\BswBundle\Controller\BswDocument;

use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Parsedown;
use Exception;

trait Document
{
    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function documentDataGenerator(Arguments $args)
    {
        try {
            $file = $this->getFilePath("{$args->title}.md", 'doc');
            $md = file_get_contents($file);
        } catch (Exception $e) {
            throw new Exception("The document is not found");
        }

        $parseMarkdown = new Parsedown();
        $document = $parseMarkdown->text($md);

        return compact('document');
    }

    /**
     * Document index
     *
     * @Route("/bsw/document/{title}", name="app_bsw_document", requirements={"title": "[\w\-]+"})
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
        $this->removeSrcCss(['ant-d', 'bsw', 'animate']);

        return $this->showEmpty('layout/document.html', ['args' => compact('title')]);
    }
}