<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Carbon\Carbon;
use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Entity\Accurate;
use Leon\BswBundle\Repository\BswWorkTaskTrailRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

/**
 * @property Expr $expr
 */
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
     * @return array
     */
    public function previewQuery(): array
    {
        return [
            'limit'  => 100,
            'select' => ['bwt'],
            'join'   => [
                'bau' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['bwt.userId'],
                    'right'  => ['bau.id'],
                ],
            ],
            'sort'   => ['bwt.state' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'trail' => [
                'width' => 120,
                'align' => 'center',
                'sort'  => 5.1,
                'html'  => true,
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewFilterCorrect(Arguments $args): array
    {
        [$team] = $this->teamInfo();

        if ($team) {
            $args->condition['bau.teamId'] = $this->createFilter(Accurate::class, $team);
        }

        return [$args->filter, $args->condition];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        [$team, $leader] = $this->teamInfo();

        $operates[] = (new Button('New task', 'app_bsw_work_task_simple', 'a:bug'))
            ->setType(Button::THEME_BSW_WARNING)
            ->setClick('showIFrame')
            ->setArgs(
                [
                    'width'  => Abs::MEDIA_SM,
                    'height' => 410,
                    'title'  => $this->twigLang('New task'),
                ]
            );

        if ($leader) {
            $operates[] = new Button('New record', 'app_bsw_work_task_persistence', $this->cnf->icon_newly);
        }

        return $operates;
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        [$team, $leader] = $this->teamInfo();

        $operates[] = (new Button('Progress'))
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
            );

        if ($leader) {
            $operates[] = (new Button('Weight'))
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
                );

            $operates[] = (new Button('Edit record', 'app_bsw_work_task_persistence'))
                ->setArgs(['id' => $args->item['id']]);

            $operates[] = (new Button('Close', 'app_bsw_work_task_close'))
                ->setType(Button::THEME_DANGER)
                ->setConfirm($this->messageLang('Are you sure'))
                ->setArgs(['id' => $args->item['id']]);
        }

        return $operates;
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
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $list = $trailRepo->lister(
            [
                'limit'  => 0,
                'alias'  => 't',
                'select' => ['u.name', 't.trail', 't.addTime AS time'],
                'join'   => [
                    'u' => [
                        'entity' => BswAdminUser::class,
                        'left'   => ['t.userId'],
                        'right'  => ['u.id'],
                    ],
                ],
                'where'  => [$this->expr->eq('t.taskId', ':task')],
                'args'   => ['task' => [$args->original['id']]],
                'order'  => ['t.id' => Abs::SORT_DESC],
            ]
        );

        $trail = null;
        $lang = $this->langLatest(['cn' => 'zh-CN', 'en' => 'en'], 'en');
        $last = count($list) - 1;

        foreach ($list as $index => $item) {
            $cb = Carbon::createFromFormat(Abs::FMT_FULL, $item['time']);
            $cb = $cb->locale($lang)->diffForHumans();
            $cb = Html::tag('span', "({$cb})", ['style' => ['color' => '#ccc', 'font-size' => '12px']]);

            $trail .= str_replace('{value}', $item['time'], Abs::HTML_CODE) . ' ';
            $trail .= str_replace('{value}', $item['name'], Abs::TEXT_BLUE) . ' ';
            $trail .= $item['trail'] . ' ';
            $trail .= $cb;
            if ($index !== $last) {
                $trail .= Abs::LINE_DASHED;
            }
        }

        $modeMap = [
            'modal'  => [
                'click' => 'showModal',
                'args'  => [
                    'width'    => 800,
                    'title'    => $this->twigLang('Trail'),
                    'content'  => Html::tag('pre', $trail, ['class' => 'bsw-pre bsw-long-text']),
                    'centered' => true,
                ],
            ],
            'drawer' => [
                'click' => 'showTrailDrawer',
                'args'  => [
                    'id' => $args->original['id'],
                ],
            ],
        ];
        $mode = $modeMap['drawer'];

        $button = (new Button('lifecycle'))
            ->setType(Button::THEME_DEFAULT)
            ->setSize(Button::SIZE_SMALL)
            ->setClick($mode['click'])
            ->setArgs($mode['args']);

        $args->hooked['trail'] = $this->getButtonHtml($button);
        $args->hooked['trailHtml'] = $trail;

        return $args->hooked;
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

        [$team, $leader] = $this->teamInfo();
        if ($team) {
            $leader = $leader ? ' 🚩' : null;
            $this->cnf->copyright = "working task manager © {$this->usr('usr_account')}{$leader}";
        }

        return $this->showPreview(
            [
                'display'     => $team ? ['menu', 'header'] : [],
                'dynamic'     => 10,
                'afterModule' => [
                    'drawer' => function ($logic, $args) {
                        $trailVisible = array_column($args['preview']['list'], 'id');
                        $trailVisible = Helper::arrayValuesSetTo($trailVisible, false, true);
                        $trailVisible = Helper::jsonStringify($trailVisible);

                        return compact('trailVisible');
                    },
                ],
            ],
            [],
            'layout/preview-task.html'
        );
    }
}