<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Carbon\Carbon;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Arguments;
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
        return BswWorkTask::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('New task', 'app_bsw_work_task_simple', 'a:bug'))
                ->setType(Button::THEME_BSW_WARNING)
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'width'  => Abs::MEDIA_SM,
                        'height' => 410,
                        'title'  => $this->twigLang('New task'),
                    ]
                ),
            new Button('New record', 'app_bsw_work_task_persistence', $this->cnf->icon_newly),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Weight'))
                ->setType(Button::THEME_DEFAULT)
                ->setRoute('app_bsw_work_task_weight')
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 234,
                        'title'  => false,
                    ]
                ),
            (new Button('Progress'))
                ->setType(Button::THEME_BSW_WARNING)
                ->setRoute('app_bsw_work_task_progress')
                ->setClick('showIFrame')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 328,
                        'title'  => false,
                    ]
                ),
            (new Button('Edit record', 'app_bsw_work_task_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * @param Arguments $args
     * @param string    $left
     * @param string    $right
     *
     * @return Charm
     */
    public function previewCharmStartTime(Arguments $args, string $left = 'Ready', string $right = 'Consumed')
    {
        if ($args->item['state'] >= 3) {
            return new Charm(Abs::HTML_CODE, $args->value);
        }

        $left = $this->fieldLang($left);
        $right = $this->fieldLang($right);
        $html = Abs::HTML_CODE . Abs::LINE_DASHED;

        [$gap, $tip] = Helper::gapDateDetail(
            $args->value,
            [
                'day'    => $this->fieldLang('Day'),
                'hour'   => $this->fieldLang('Hour'),
                'minute' => $this->fieldLang('Minute'),
                'second' => $this->fieldLang('Second'),
            ]
        );

        if ($gap >= 0) {
            $html .= str_replace('{value}', "{$left}: {$tip}", Abs::HTML_GREEN_TEXT);
        } else {
            $html .= str_replace('{value}', "{$right}: {$tip}", Abs::HTML_ORANGE_TEXT);
        }

        return new Charm($html, $args->value);
    }

    /**
     * @param Arguments $args
     *
     * @return Charm
     */
    public function previewCharmEndTime(Arguments $args)
    {
        return $this->previewCharmStartTime($args, 'Surplus', 'Expired');
    }

    /**
     * @param Arguments $args
     *
     * @return Charm
     */
    public function previewCharmTrail(Arguments $args)
    {
        $value = $args->value;
        if (empty($value)) {
            $value = 'Without any trail.';
        } else {
            $value = array_reverse(explode(PHP_EOL, $value));
            foreach ($value as &$item) {
                preg_match_all('/\[([0-9\-: ]+)\]/', $item, $result);

                $date = Carbon::createFromFormat(Abs::FMT_FULL, current($result[1]));
                $human = $date->locale('zh-CN')->diffForHumans();
                $html = str_replace('{value}', current($result[1]), Abs::HTML_CODE);

                $item .= ' ';
                $item .= Html::tag('span', "({$human})", ['style' => ['color' => '#ccc', 'font-size' => '12px']]);
                $item = str_replace(current($result[0]), $html, $item);
            }
            $value = implode(Abs::LINE_DASHED, $value);
        }

        return $this->charmShowContent('Trail', $value, ['width' => 800]);
    }

    /**
     * Preview record
     *
     * @Route("/bsw-work-task/preview", name="app_bsw_work_task_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview(
            [
                'display' => ['menu', 'header', 'footer'],
                'dynamic' => 10,
            ]
        );
    }
}