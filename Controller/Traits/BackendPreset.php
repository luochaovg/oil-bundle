<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminPersistenceLog;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Repository\BswAdminPersistenceLogRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Monolog\Logger;

/**
 * @property TranslatorInterface $translator
 * @property Logger              $logger
 */
trait BackendPreset
{
    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function acmeEnumExtraRouteName(Arguments $args): array
    {
        return $this->routeKVP(false);
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function acmeEnumExtraCommand(Arguments $args): array
    {
        $result = $this->commandCaller('list', ['--format' => 'json']);
        $result = Helper::parseJsonString($result);

        $commands = [];
        foreach ($result['commands'] ?? [] as $item) {
            $name = $item['name'];
            foreach ($this->parameters('command_queue_pos') as $pos) {
                if (strpos($name, $pos) === 0) {
                    $commands[$name] = $item['description'];
                }
            }
        }

        return $commands;
    }

    /**
     * @param $current
     * @param $hooked
     * @param $original
     *
     * @return Charm|string
     */
    public function previewCharmHeadTime($current, $hooked, $original)
    {
        if ($original > time()) {
            return new Charm(Abs::HTML_GREEN, $current);
        }

        return new Charm(Abs::HTML_GRAY, $current);
    }

    /**
     * @param $current
     * @param $hooked
     * @param $original
     *
     * @return Charm|string
     */
    public function previewCharmTailTime($current, $hooked, $original)
    {
        return $this->previewCharmHeadTime($current, $hooked, $original);
    }

    /**
     * @param array $hooked
     *
     * @return array
     */
    public function clonePreviewToForm(array $hooked): array
    {
        $hooked = Helper::arrayRemove($hooked, ['id']);
        foreach ($hooked as &$value) {
            if (!is_scalar($value)) {
                continue;
            }
            $value = ltrim($value, '$￥');
        }

        return ['fill' => $hooked];
    }

    /**
     * @param array $filter
     * @param array $index
     * @param bool  $arrayValueToString
     *
     * @return array
     */
    public function previewFilter(array $filter, array $index = [], bool $arrayValueToString = true): array
    {
        $_filter = [];
        foreach ($filter as $key => $value) {

            $k = Helper::camelToUnderWithNumeric($key);
            if (!is_numeric(Helper::arrayLatestItem($k))) {
                $k = "{$k}_" . ($index[$key] ?? 0);
            }

            if (is_scalar($value)) {
                $_filter[$k] = $value;
            } elseif (is_array($value)) {
                if ($arrayValueToString) {
                    $_filter[$k] = implode(Abs::FORM_DATA_SPLIT, $value);
                } else {
                    foreach ($value as $index => $item) {
                        $_filter[$k][$index] = $item;
                    }
                }
            }
        }

        return ['filter' => $_filter];
    }

    /**
     * Get button html
     *
     * @param Button $button
     * @param bool   $vue
     *
     * @return string
     */
    public function getButtonHtml(Button $button, bool $vue = false): string
    {
        $twig = $vue ? 'form/button.html' : 'form/button.native.html';

        return $this->renderPart($twig, ['form' => $button]);
    }

    /**
     * @param string $label
     * @param string $content
     * @param array  $options
     *
     * @return Charm
     */
    public function charmShowContent(string $label, string $content, array $options = []): Charm
    {
        $args = [
            'title'        => $this->twigLang($label),
            'content'      => Html::tag('pre', $content, ['class' => 'bsw-pre bsw-long-text']),
            'closable'     => false,
            'keyboard'     => true,
            'maskClosable' => true,
        ];

        $button = (new Button('{value}'))
            ->setSize(Abs::SIZE_SMALL)
            ->setType(Abs::THEME_DEFAULT)
            ->setClick('showModal')
            ->setArgs(array_merge($options, $args));

        return new Charm($this->getButtonHtml($button), $label);
    }

    /**
     * Database operation logger
     *
     * @param string $entity
     * @param int    $type
     * @param array  $before
     * @param array  $later
     * @param array  $effect
     *
     * @throws
     */
    public function databaseOperationLogger(
        string $entity,
        int $type,
        array $before = [],
        array $later = [],
        array $effect = []
    ) {
        if (!$this->parameter('backend_db_logger')) {
            return;
        }

        /**
         * @var BswAdminPersistenceLogRepository $loggerRepo
         */
        $loggerRepo = $this->repo(BswAdminPersistenceLog::class);
        $result = $loggerRepo->newly(
            [
                'table'  => Helper::tableNameFromCls($entity),
                'userId' => $this->usr('usr_uid') ?? 0,
                'type'   => $type,
                'before' => Helper::jsonStringify($before),
                'later'  => Helper::jsonStringify($later),
                'effect' => Helper::jsonStringify($effect),
            ]
        );

        if ($result === false) {
            $this->logger->error("Database operation logger error: {$loggerRepo->pop()}");
        }
    }

    /**
     * @param string $flag
     * @param array  $option
     *
     * @return array
     */
    public function uploadOptionsHandler(string $flag, array $option): array
    {
        if ($flag === 'mixed') {
            return array_merge(
                $option,
                [
                    'fileFn' => function ($file) {
                        $file->href = 'app_bsw_attachment_preview';

                        return $file;
                    },
                ]
            );
        }

        return $option;
    }
}