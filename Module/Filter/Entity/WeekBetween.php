<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Type;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Filter;

class WeekBetween extends Filter
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
     * @param mixed $value
     *
     * @return array
     * @throws
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
        [$nameFrom, $nameTo] = $this->nameBuilder(['from', 'to']);

        return [
            "{$field} BETWEEN :{$nameFrom} AND :{$nameTo}",
            [
                $nameFrom => $from,
                $nameTo   => $to,
            ],
            [
                $nameFrom => is_numeric($from) ? Type::FLOAT : Type::STRING,
                $nameTo   => is_numeric($to) ? Type::FLOAT : Type::STRING,
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
        [$nameFrom, $nameTo] = $this->nameBuilder(['from', 'to']);

        return [
            $this->expr->between($field, ":{$nameFrom}", ":{$nameTo}"),
            [
                $nameFrom => $from,
                $nameTo   => $to,
            ],
            [
                $nameFrom => is_numeric($from) ? Type::FLOAT : Type::STRING,
                $nameTo   => is_numeric($to) ? Type::FLOAT : Type::STRING,
            ],
        ];
    }
}