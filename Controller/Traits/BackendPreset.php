<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property TranslatorInterface $translator
 */
trait BackendPreset
{
    /**
     * @param array $enum
     *
     * @return array
     */
    public function acmeEnumExtraRouteName(array $enum): array
    {
        return $this->routeKVP(false);
    }

    /**
     * @param array $enum
     *
     * @return array
     */
    public function acmeEnumExtraCommand(array $enum): array
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
            $value = ltrim($value, '$￥');
        }

        return ['fill' => $hooked];
    }

    /**
     * @param string $field
     * @param string $content
     * @param array  $options
     *
     * @return Charm
     */
    public function charmShowContent(string $field, string $content, array $options = []): Charm
    {
        $label = Helper::stringToLabel($field);
        $args = [
            'title'    => $this->translator->trans($label, [], 'twig'),
            'content'  => Html::tag('pre', $content, ['class' => 'bsw-pre bsw-long-text']),
            'centered' => true,
        ];

        $button = (new Button($label))
            ->setSize(Button::SIZE_SMALL)
            ->setType(Button::THEME_DASHED)
            ->setClick('showModal')
            ->setArgs(array_merge($options, $args));

        $button = $this->renderPart('@LeonBsw/form/button.native', ['form' => $button]);

        return new Charm($button);
    }
}