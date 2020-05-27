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
    const EQ       = 1;
    const EQ_LABEL = 'Expr equal';

    const NEQ       = 2;
    const NEQ_LABEL = 'Expr not equal';

    const GT       = 3;
    const GT_LABEL = 'Expr greater than';

    const GTE       = 4;
    const GTE_LABEL = 'Expr greater than or equal to';

    const LT       = 5;
    const LT_LABEL = 'Expr less than';

    const LTE       = 6;
    const LTE_LABEL = 'Expr less than or equal to';

    const IN       = 7;
    const IN_LABEL = 'Expr in';

    const NOT_IN       = 8;
    const NOT_IN_LABEL = 'Expr not in';

    const IS_NULL       = 9;
    const IS_NULL_LABEL = 'Expr is null';

    const IS_NOT_NULL       = 10;
    const IS_NOT_NULL_LABEL = 'Expr is not null';

    const LIKE       = 11;
    const LIKE_LABEL = 'Expr contain';

    const BEGIN_LIKE       = 12;
    const BEGIN_LIKE_LABEL = 'Expr begin contain';

    const END_LIKE       = 13;
    const END_LIKE_LABEL = 'Expr end contain';

    const NOT_LIKE       = 14;
    const NOT_LIKE_LABEL = 'Expr not contain';

    const BETWEEN       = 15;
    const BETWEEN_LABEL = 'Expr between';

    /**
     * @cosnt array
     */
    const MODE = [
        self::EQ          => self::EQ_LABEL,
        self::NEQ         => self::NEQ_LABEL,
        self::GT          => self::GT_LABEL,
        self::GTE         => self::GTE_LABEL,
        self::LT          => self::LT_LABEL,
        self::LTE         => self::LTE_LABEL,
        self::IN          => self::IN_LABEL,
        self::NOT_IN      => self::NOT_IN_LABEL,
        self::IS_NULL     => self::IS_NULL_LABEL,
        self::IS_NOT_NULL => self::IS_NOT_NULL_LABEL,
        self::LIKE        => self::LIKE_LABEL,
        self::BEGIN_LIKE  => self::BEGIN_LIKE_LABEL,
        self::END_LIKE    => self::END_LIKE_LABEL,
        self::NOT_LIKE    => self::NOT_LIKE_LABEL,
        self::BETWEEN     => self::BETWEEN_LABEL,
    ];

    /**
     * @cosnt array
     */
    const MODE_INTEGER = [
        self::EQ      => self::EQ_LABEL,
        self::NEQ     => self::NEQ_LABEL,
        self::IN      => self::IN_LABEL,
        self::NOT_IN  => self::NOT_IN_LABEL,
        self::GT      => self::GT_LABEL,
        self::GTE     => self::GTE_LABEL,
        self::LT      => self::LT_LABEL,
        self::LTE     => self::LTE_LABEL,
        self::BETWEEN => self::BETWEEN_LABEL,
    ];

    /**
     * @cosnt array
     */
    const MODE_NUMERIC = [
        self::GT      => self::GT_LABEL,
        self::GTE     => self::GTE_LABEL,
        self::LT      => self::LT_LABEL,
        self::LTE     => self::LTE_LABEL,
        self::BETWEEN => self::BETWEEN_LABEL,
    ];

    /**
     * @cosnt array
     */
    const MODE_STRING = [
        self::EQ          => self::EQ_LABEL,
        self::NEQ         => self::NEQ_LABEL,
        self::IS_NULL     => self::IS_NULL_LABEL,
        self::IS_NOT_NULL => self::IS_NOT_NULL_LABEL,
        self::LIKE        => self::LIKE_LABEL,
        self::BEGIN_LIKE  => self::BEGIN_LIKE_LABEL,
        self::END_LIKE    => self::END_LIKE_LABEL,
        self::NOT_LIKE    => self::NOT_LIKE_LABEL,
    ];

    /**
     * @param mixed $value
     *
     * @return array
     * @throws
     */
    public function parse($value): array
    {
        $this->expression = $value[0] ?? null;
        $value = $value[1] ?? null;

        if (empty($this->expression)) {
            throw new FilterException("Give filter expression first");
        }

        if (!isset(self::MODE[$this->expression])) {
            throw new FilterException("Filter expression is not support");
        }

        if (is_null($value)) {
            if (!in_array($this->expression, [self::IS_NULL, self::IS_NOT_NULL])) {
                throw new FilterException("Value for filter expression is required");
            }
            $value = '';
        }

        $value = Helper::stringToArray($value, false, false, null, Abs::FORM_DATA_SPLIT);
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
                throw new FilterException('The filter arguments is not enough');
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
                throw new FilterException('The filter arguments is not enough');
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