<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Min extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Length greater than or equal to';
    }

    /**
     * @inheritdoc
     */
    protected function message(): string
    {
        return '{{ field }} Length must greater than or equal to {{ arg1 }}';
    }

    /**
     * @inheritdoc
     */
    protected function proveArgs(): bool
    {
        return is_numeric(current($this->args));
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        $length = is_array($this->value) ? count($this->value) : Helper::strLen($this->value);

        return $length >= current($this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}