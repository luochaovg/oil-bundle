<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Doctrine\DBAL\Connection;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property TranslatorInterface $translator
 */
trait SiteIndex
{
    /**
     * @return array
     */
    public function siteIndexAnnotation()
    {
        return [
            'id'    => [
                'width'  => 80,
                'align'  => 'center',
                'render' => Abs::RENDER_CODE,
            ],
            'name'  => [
                'width'  => 250,
                'align'  => 'right',
                'render' => Abs::RENDER_CODE,
            ],
            'value' => [
                'width' => 750,
            ],
        ];
    }

    /**
     * @return string
     */
    public function siteIndexWelcome(): string
    {
        return $this->translator->trans(
            "Welcome {{ user }}, today is {{ today }}",
            [
                '{{ user }}'  => $this->usr->{$this->cnf->usr_account},
                '{{ today }}' => date(Abs::FMT_DAY),
            ]
        );
    }

    /**
     * Welcome page
     *
     * @Route("/", name="app_site_index")
     *
     * @return Response
     * @throws
     */
    public function siteIndex(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        /**
         * @param string $label
         *
         * @return string
         */
        $lang = function (string $label): string {
            return $this->translator->trans($label, [], 'twig');
        };

        /**
         * @var Connection $pdo
         */
        $pdo = $this->pdo();
        $version = $pdo->fetchAssoc('SELECT VERSION() AS version');

        $list = [
            [
                'name'  => $lang('Server protocol'),
                'value' => $_SERVER['SERVER_PROTOCOL'],
            ],
            [
                'name'  => $lang('Gateway interface'),
                'value' => $_SERVER['GATEWAY_INTERFACE'],
            ],
            [
                'name'  => $lang('Server software'),
                'value' => $_SERVER['SERVER_SOFTWARE'],
            ],
            [
                'name'  => $lang('Service address'),
                'value' => $_SERVER['SERVER_ADDR'],
            ],
            [
                'id'    => 5,
                'name'  => $lang('Service port'),
                'value' => $_SERVER['SERVER_PORT'],
            ],
            [
                'name'  => $lang('Remote address'),
                'value' => $_SERVER['REMOTE_ADDR'],
            ],

            [
                'name'  => $lang('PHP version'),
                'value' => PHP_VERSION,
            ],
            [
                'name'  => $lang('Zend version'),
                'value' => Zend_Version(),
            ],
            [
                'id'    => 8,
                'name'  => $lang('MySQL version'),
                'value' => current($version),
            ],
            [
                'name'  => $lang('PHP uname'),
                'value' => php_uname(),
            ],
            [
                'name'  => $lang('Http user agent'),
                'value' => $_SERVER['HTTP_USER_AGENT'],
            ],
            [
                'name'  => $lang('Backend framework'),
                'value' => 'Symfony ' . $this->kernel::VERSION,
            ],
            [
                'name'  => $lang('Frontend framework'),
                'value' => 'Ant Design for Vue',
            ],
        ];

        $index = 0;
        foreach ($list as &$item) {
            $item['id'] = ++$index;
        }

        return $this->showPreview(['preview' => $list]);
    }
}