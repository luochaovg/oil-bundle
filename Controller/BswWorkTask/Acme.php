<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Carbon\Carbon;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Controller\Traits\WorkTask;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Bsw work task
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
    use WorkTask;

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraUserId(Arguments $args): array
    {
        [$team] = $this->workTaskTeam();

        if ($team) {
            $filter = [
                'where' => [$this->expr->eq('kvp.teamId', ':team')],
                'args'  => ['team' => [$team]],
            ];
        } else {
            $filter = [
                'where' => [$this->expr->gt('kvp.teamId', ':team')],
                'args'  => ['team' => [0]],
            ];
        }

        $teamFilter = [
            'join' => [
                'bwt' => [
                    'entity' => BswWorkTeam::class,
                    'left'   => ['kvp.teamId'],
                    'right'  => ['bwt.id'],
                ],
            ],
        ];

        return $this->repo(BswAdminUser::class)->kvp(
            ['kvp.name', 'bwt.name AS team'],
            Abs::PK,
            function ($v) {
                return "[{$v['team']}] {$v['name']}";
            },
            $filter,
            $teamFilter
        );
    }

    /**
     * Trail list to string
     *
     * @param array $list
     *
     * @return string
     */
    public function trailListStringify(array $list): string
    {
        $trail = null;
        $lang = $this->langLatest(['cn' => 'zh-CN', 'en' => 'en'], 'en');

        foreach ($list as $item) {
            $cb = Carbon::createFromFormat(Abs::FMT_FULL, $item['time']);
            $cb = $cb->locale($lang)->diffForHumans();
            $cb = Html::tag('span', "({$cb})", ['style' => ['color' => '#ccc', 'font-size' => '12px']]);

            $trail .= str_replace('{value}', $item['time'], Abs::HTML_CODE) . ' ';
            $trail .= str_replace('{value}', $item['name'], Abs::TEXT_BLUE) . ' ';
            $trail .= $item['trail'] . ' ';
            $trail .= $cb;
            $trail .= str_replace(10, 15, Abs::LINE_DASHED);
        }

        return $trail;
    }
}