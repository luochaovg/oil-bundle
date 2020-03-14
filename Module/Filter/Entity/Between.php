<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Type;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Filter;

class Between extends Filter
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
        if (is_string($value)) {
            $value = explode(Abs::FORM_DATA_SPLIT, $value);
        }

        if (!is_array($value) || count($value) < 2) {
            throw new FilterException(self::class . ' got invalid value for parse, given array please');
        }

        $from = trim($value[0]);
        $to = trim($value[1]);

        if (!$this->timestamp) {
            return [$from, $to];
        }

        return [strtotime($from), strtotime($to)];
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