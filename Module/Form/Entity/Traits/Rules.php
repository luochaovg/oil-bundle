<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait Rules
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @return array
     */
    public function getRulesArray(): array
    {
        return $this->rules;
    }

    /**
     * @return string
     */
    public function getRules(): string
    {
        return Helper::jsonStringify($this->rules);
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function appendRules(array $rules)
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }
}