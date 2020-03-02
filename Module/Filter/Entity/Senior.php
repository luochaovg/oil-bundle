<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Filter;

/**
 * @property Expr $expr
 */
class Senior extends Filter
{
    /**
     * @var int
     */
    protected $expression;

    /**
     * @const int
     */
    const EQ          = 1;
    const NEQ         = 2;
    const GT          = 3;
    const GTE         = 4;
    const LT          = 5;
    const LTE         = 6;
    const IN          = 7;
    const NOT_IN      = 8;
    const IS_NULL     = 9;
    const IS_NOT_NULL = 10;
    const LIKE        = 11;
    const BEGIN_LIKE  = 12;
    const END_LIKE    = 13;
    const NOT_LIKE    = 14;
    const BETWEEN     = 15;

    /**
     * @cosnt array
     */
    const MODE = [
        self::EQ          => 'Expr equal',
        self::NEQ         => 'Expr not equal',
        self::GT          => 'Expr greater than',
        self::GTE         => 'Expr greater than or equal to',
        self::LT          => 'Expr less than',
        self::LTE         => 'Expr less than or equal to',
        self::IN          => 'Expr in',
        self::NOT_IN      => 'Expr not in',
        self::IS_NULL     => 'Expr is null',
        self::IS_NOT_NULL => 'Expr is not null',
        self::LIKE        => 'Expr contain',
        self::BEGIN_LIKE  => 'Expr begin contain',
        self::END_LIKE    => 'Expr end contain',
        self::NOT_LIKE    => 'Expr not contain',
        self::BETWEEN     => 'Expr between',
    ];

    /**
     * @param mixed $value
     *
     * @return array
     * @throws
     */
    public function parse($value): array
    {
        if (isset($value[0])) {
            $this->expression = $value[0];
        }

        if (!isset(self::MODE[$this->expression])) {
            throw new FilterException("Filter expression is unknown or it's not support");
        }

        if (is_null($value[1] ?? null)) {
            throw new FilterException("Value for filter expression is required");
        }

        $value = explode(Abs::FORM_DATA_SPLIT, $value[1]);
        $value = Helper::numericValues($value);

        return $value;
    }

    /**
     * SQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     * @throws
     */
    public function sql(string $field, array $item): array
    {
        [$targetKey, $firstKey, $secondKey] = $this->nameBuilder(['target', 'first', 'second']);

        $target = $item[0] ?? null;
        $first = $target;
        $second = $item[1] ?? null;

        if ($this->expression == self::EQ) {
            return [
                "{$field} = :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::NEQ) {
            return [
                "{$field} <> :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::GT) {
            return [
                "{$field} > :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::GTE) {
            return [
                "{$field} >= :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::LT) {
            return [
                "{$field} < :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::LTE) {
            return [
                "{$field} <= :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::IN) {
            return [null];
        }

        if ($this->expression == self::NOT_IN) {
            return [null];
        }

        if ($this->expression == self::IS_NULL) {
            return ["{$field} IS NULL"];
        }

        if ($this->expression == self::IS_NOT_NULL) {
            return ["{$field} IS NOT NULL"];
        }

        if ($this->expression == self::LIKE) {
            return ["{$field} LIKE '%{$target}%'"];
        }

        if ($this->expression == self::BEGIN_LIKE) {
            return ["{$field} LIKE '{$target}%'"];
        }

        if ($this->expression == self::END_LIKE) {
            return ["{$field} LIKE '%{$target}'"];
        }

        if ($this->expression == self::NOT_LIKE) {
            return ["{$field} NOT LIKE '%{$target}%'"];
        }

        if ($this->expression == self::BETWEEN) {

            if (is_null($second)) {
                throw new FilterException('The filter arguments is not enough.');
            }

            return [
                "{$field} BETWEEN :{$firstKey} AND :{$secondKey}",
                [
                    $firstKey  => $first,
                    $secondKey => $second,
                ],
                [
                    $firstKey  => is_numeric($first) ? Type::FLOAT : Type::STRING,
                    $secondKey => is_numeric($second) ? Type::FLOAT : Type::STRING,
                ],
            ];
        }

        return [null];
    }

    /**
     * DQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     * @throws
     */
    public function dql(string $field, array $item)
    {
        [$targetKey, $firstKey, $secondKey] = $this->nameBuilder(['target', 'first', 'second']);

        $target = $item[0] ?? null;
        $first = $target;
        $second = $item[1] ?? null;

        if ($this->expression == self::EQ) {
            return [
                $this->expr->eq($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::NEQ) {
            return [
                $this->expr->neq($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::GT) {
            return [
                $this->expr->gt($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::GTE) {
            return [
                $this->expr->gte($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::LT) {
            return [
                $this->expr->lt($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::LTE) {
            return [
                $this->expr->lte($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Type::FLOAT : Type::STRING],
            ];
        }

        if ($this->expression == self::IN) {
            return [$this->expr->in($field, $item)];
        }

        if ($this->expression == self::NOT_IN) {
            return [$this->expr->notIn($field, $item)];
        }

        if ($this->expression == self::IS_NULL) {
            return [$this->expr->isNull($field)];
        }

        if ($this->expression == self::IS_NOT_NULL) {
            return [$this->expr->isNotNull($field)];
        }

        if ($this->expression == self::LIKE) {
            return [$this->expr->like($field, $this->expr->literal("%{$target}%"))];
        }

        if ($this->expression == self::BEGIN_LIKE) {
            return [$this->expr->like($field, $this->expr->literal("{$target}%"))];
        }

        if ($this->expression == self::END_LIKE) {
            return [$this->expr->like($field, $this->expr->literal("%{$target}"))];
        }

        if ($this->expression == self::NOT_LIKE) {
            return [$this->expr->notLike($field, $this->expr->literal("%{$target}%"))];
        }

        if ($this->expression == self::BETWEEN) {

            if (is_null($second)) {
                throw new FilterException('The filter arguments is not enough.');
            }

            return [
                $this->expr->between($field, ":{$firstKey}", ":{$secondKey}"),
                [
                    $firstKey  => $first,
                    $secondKey => $second,
                ],
                [
                    $firstKey  => is_numeric($first) ? Type::FLOAT : Type::STRING,
                    $secondKey => is_numeric($second) ? Type::FLOAT : Type::STRING,
                ],
            ];
        }

        return [null];
    }
}