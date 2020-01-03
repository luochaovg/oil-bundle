<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Required extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is required';
    }

    /**
     * @inheritdoc
     */
    protected function message(): string
    {
        return '{{ field }} Required';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return isset($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}
