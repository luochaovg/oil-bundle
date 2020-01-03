<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Validator\Validator;

class MysqlUnsSmallint extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is mysql unsigned smallint';
    }

    /**
     * @inheritdoc
     */
    protected function message(): string
    {
        return '{{ field }} Must between {{ arg1 }} and {{ arg2 }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!Helper::isIntNumeric($this->value)) {
            return false;
        }

        return $this->value >= Abs::MYSQL_SMALLINT_UNS_MIN && $this->value <= Abs::MYSQL_SMALLINT_UNS_MAX;
    }

    /**
     * @inheritdoc
     */
    public function arrayArgs(): array
    {
        return [Abs::MYSQL_SMALLINT_UNS_MIN, Abs::MYSQL_SMALLINT_UNS_MAX];
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return intval($this->value);
    }
}