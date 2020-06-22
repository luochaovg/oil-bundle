<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Type;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Filter;

class WeekIntersect extends Filter
{
    /**
     * @var bool
     */
    protected $timestamp = false;

    /**
     * @return bool
     */
    public function isTimestamp(): bool
    {
        return $this->timestamp;
    }

    /**
     * @param bool $timestamp
     *
     * @return $this
     */
    public function setTimestamp(bool $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @var array
     */
    protected $alias = ['from' => 'x.startTime', 'to' => 'x.endTime'];

    /**
     * @param string $index
     *
     * @return array|string
     */
    public function getAlias(string $index = null)
    {
        return $index ? ($this->alias[$index] ?? null) : $this->alias;
    }

    /**
     * @param array $alias
     *
     * @return $this
     */
    public function setAlias(array $alias)
    {
        $this->alias = array_merge($this->alias, $alias);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    public function parse($value)
    {
        [$year, $week] = explode('-', $value);
        [$begin, $end] = Helper::yearWeekToDate($year, $week);

        $begin = "{$begin} " . Abs::DAY_BEGIN;
        $end = "{$end} " . Abs::DAY_END;

        if ($this->isTimestamp()) {
            return [strtotime($begin), strtotime($end)];
        }

        return [$begin, $end];
    }

    /**
     * SQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     */
    public function sql(string $field, array $item): array
    {
        [$from, $to] = $item;
        [$fromName, $toName] = $this->nameBuilder(['from', 'to']);

        $fromField = $this->getAlias('from');
        $toField = $this->getAlias('to');

        return [
            "({$fromField} BETWEEN :{$fromName} AND :{$fromName}) OR ({$toField} BETWEEN :{$fromName} AND :{$fromName}) OR ({$fromField} < :{$fromName} AND {$toField} > :{$toName})",
            [
                $fromName => $from,
                $toName   => $to,
            ],
            [
                $fromName => is_numeric($from) ? Type::FLOAT : Type::STRING,
                $toName   => is_numeric($to) ? Type::FLOAT : Type::STRING,
            ],
        ];
    }

    /**
     * DQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     */
    public function dql(string $field, array $item)
    {
        [$from, $to] = $item;
        [$fromName, $toName] = $this->nameBuilder(['from', 'to']);

        $fromField = $this->getAlias('from');
        $toField = $this->getAlias('to');


        return [
            $this->expr->orX(
                $this->expr->between($fromField, ":{$fromName}", ":{$toName}"),
                $this->expr->between($toField, ":{$fromName}", ":{$toName}"),
                $this->expr->andX(
                    $this->expr->lt($fromField, ":{$fromName}"),
                    $this->expr->gt($toField, ":{$toName}")
                )
            ),
            [
                $fromName => $from,
                $toName   => $to,
            ],
            [
                $fromName => is_numeric($from) ? Type::FLOAT : Type::STRING,
                $toName   => is_numeric($to) ? Type::FLOAT : Type::STRING,
            ],
        ];
    }
}