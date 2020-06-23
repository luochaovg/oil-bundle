<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Entity\Between;
use Leon\BswBundle\Module\Filter\Entity\TeamMember;
use Leon\BswBundle\Module\Form\Entity\SelectTree;
use Leon\BswBundle\Module\Form\Entity\Week;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property Expr $expr
 */
trait WeekReport
{
    /**
     * @var array
     */
    protected $weekReportAlias = [
        'team'   => ['u', 'teamId'],
        'member' => ['tt', 'userId'],
    ];

    /**
     * @return string
     */
    public function weekReportEntity(): string
    {
        return BswWorkTaskTrail::class;
    }

    /**
     * @return array
     */
    public function weekReportFilterAnnotationOnly(): array
    {
        [$team] = $this->workTaskTeam();

        return [
            'team' => [
                'label'      => 'User id',
                'field'      => 'u.teamId',
                'type'       => SelectTree::class,
                'typeArgs'   => ['treeData' => $this->getTeamMemberTree($team), 'expandAll' => true],
                'filter'     => TeamMember::class,
                'filterArgs' => ['alias' => $this->weekReportAlias],
                'column'     => 3,
            ],
            'week' => [
                'label'      => 'Week n',
                'field'      => 'tt.addTime',
                'type'       => Week::class,
                'filter'     => Between::class,
                'filterArgs' => ['weekValue' => true, 'carryTime' => false],
            ],
        ];
    }

    /**
     * @return array
     */
    public function weekReportQuery(): array
    {
        return [
            'paging' => false,
            'select' => ['u.name', 't.title', 'tt.trail', 'tt.addTime AS time'],
            'alias'  => 'tt',
            'join'   => [
                't' => [
                    'entity' => BswWorkTask::class,
                    'left'   => ['tt.taskId'],
                    'right'  => ['t.id'],
                ],
                'u' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['t.userId'],
                    'right'  => ['u.id'],
                ],
            ],
            'where'  => [
                $this->expr->eq('tt.reliable', ':reliable'),
                $this->expr->eq('tt.state', ':state'),
            ],
            'args'   => [
                'reliable' => [1],
                'state'    => [Abs::NORMAL],
            ],
            'sort'   => ['tt.id' => Abs::SORT_ASC],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function weekReportFilterCorrect(Arguments $args): array
    {
        $args->condition = $this->correctTeamMemberFilter(
            'u.teamId',
            $this->weekReportAlias,
            $args->condition ?? null
        );

        return [$args->filter, $args->condition];
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function weekReportAfterHook(Arguments $args)
    {
        $args->hooked = current($this->taskTrailHandler([$args->hooked]));

        return $args->hooked;
    }

    /**
     * @return array
     */
    public function weekReportTabsLinks(): array
    {
        return $this->tabsLinks();
    }

    /**
     * Week report
     *
     * @Route("/bsw-work-week-report", name="app_bsw_work_week_report")
     * @Access()
     *
     * @return Response
     */
    public function weekReport(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview([], [], 'task/week-report.html');
    }
}