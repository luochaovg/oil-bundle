<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Leon\BswBundle\Component\Reflection;
use ReflectionClassConstant;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

/**
 * @property TranslatorInterface $translator
 */
trait EnumDict
{
    /**
     * @return array
     */
    public function enumDictAnnotation()
    {
        return [
            'key'  => [
                'width' => 300,
                'align' => 'right',
                'html'  => true,
            ],
            'enum' => [
                'width' => 700,
                'html'  => true,
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function enumDictBeforeHook(Arguments $args): array
    {
        $key = Html::tag('div', $args->original['key'], ['class' => 'bsw-code bsw-long-text']);
        $info = Html::tag(
            'span',
            $args->original['info'],
            [
                'style' => [
                    'display' => 'block',
                    'margin'  => '6px 0',
                    'color'   => '#ccc',
                ],
            ]
        );

        $enum = [];
        foreach ($args->original['enum'] as $k => $v) {
            $k = Html::tag('div', $k, ['class' => 'ant-tag ant-tag-has-color', 'style' => ['color' => '#1890ff']]);
            $v = Html::tag('div', $v, ['class' => 'bsw-code bsw-long-text']);
            array_push($enum, "{$k} => {$v}");
        }

        $enum = Html::tag(
            'div',
            implode(Abs::LINE_DASHED, $enum),
            ['style' => ['margin' => '0 20px']]
        );

        return [
            'id'   => $args->original['key'],
            'key'  => "{$key}<br>{$info}",
            'enum' => $enum,
        ];
    }

    /**
     * @return array
     */
    public function enumDictQuery(): array
    {
        return ['limit' => 0];
    }

    /**
     * Enum dict
     *
     * @Route("/enum-dict", name="app_enum_dict")
     * @Access()
     *
     * @return Response
     * @throws
     */
    public function enumDict(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $reflection = new Reflection();
        $constant = $reflection->getClsConstDoc(static::$enum, true);

        $list = [];
        foreach ($constant as $key => $item) {
            /**
             * @var ReflectionClassConstant $proto
             */
            $proto = $item['proto'];
            $enum = $proto->getValue();
            $enum = $this->enumLang($enum);
            array_push(
                $list,
                [
                    'key'  => $key,
                    'info' => $item['const'],
                    'enum' => $enum,
                ]
            );
        }

        return $this->showPreview(['preview' => $list]);
    }
}