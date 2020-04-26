<?php

namespace Leon\BswBundle\Controller\BswAdminPersistenceLog;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminPersistenceLog;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Entity\JsonStringify;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminPersistenceLog::class;
    }

    /**
     * @return array
     */
    public function previewQuery(): array
    {
        return [
            'alias'  => 'pl',
            'select' => ['pl', 'u.name AS userId'],
            'join'   => [
                'u' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['pl.userId'],
                    'right'  => ['u.id'],
                ],
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            // new Button('New record', 'app_bsw_admin_persistence_log_persistence', 'a:plus'),
        ];
    }

    /**
     * @param array $current
     * @param array $hooked
     * @param array $origin
     *
     * @return Button[]
     */
    public function previewRecordOperates(array $current, array $hooked, array $origin): array
    {
        return [
            // (new Button('Edit record', 'app_bsw_admin_persistence_log_persistence'))->setArgs(['id' => $current['id']]),
        ];
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return Charm
     */
    protected function printJson(string $field, string $value): Charm
    {
        $value = Html::cleanHtml($value, true);

        if (substr_count($value, "\n") + 1 < 25) {
            return new Charm(Abs::HTML_TEXT, $value);
        }

        return $this->charmShowContent(
            $field,
            Html::tag(
                'div',
                $value,
                [
                    'class' => 'bsw-code bsw-long-text',
                    'style' => ['width' => '99.6%', 'padding' => '12px'],
                ]
            ),
            ['width' => 600]
        );
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function previewCharmBefore($value)
    {
        return $this->printJson('before', $value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function previewCharmLater($value)
    {
        return $this->printJson('later', $value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function previewCharmEffect($value)
    {
        return $this->printJson('effect', $value);
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-persistence-log/preview", name="app_bsw_admin_persistence_log_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}