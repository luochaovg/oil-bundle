<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait FormRules
{
    /**
     * Form rule required
     *
     * @param bool        $whitespace
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleRequired(bool $whitespace = true, string $message = null): array
    {
        $rule = Abs::RULES_REQUIRED;
        $rule['whitespace'] = $whitespace;
        $rule['message'] = $message ?? $rule['message'];

        return $rule;
    }

    /**
     * Form rule length
     *
     * @param int         $len
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleLen(int $len, string $message = null): array
    {
        return [
            'len'     => $len,
            'message' => $message ?? '{{ field }} Length must equal to {{ arg1 }}',
            'args'    => ['{{ arg1 }}' => $len],
        ];
    }

    /**
     * Form rule min
     *
     * @param int         $min
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleMin(int $min, string $message = null): array
    {
        return [
            'min'     => $min,
            'message' => $message ?? '{{ field }} Length must greater than or equal to {{ arg1 }}',
            'args'    => ['{{ arg1 }}' => $min],
        ];
    }

    /**
     * Form rule max
     *
     * @param int         $max
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleMax(int $max, string $message = null): array
    {
        return [
            'max'     => $max,
            'message' => $message ?? '{{ field }} Length must less than or equal to {{ arg1 }}',
            'args'    => ['{{ arg1 }}' => $max],
        ];
    }

    /**
     * Form rule pattern
     *
     * @param string $pattern
     * @param string $message
     *
     * @return array
     */
    public function formRulePattern(string $pattern, string $message): array
    {
        return [
            'pattern' => $pattern,
            'message' => $message,
        ];
    }

    /**
     * Form rule url
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleUrl(string $message = null): array
    {
        return [
            'type'    => 'url',
            'message' => $message ?? '{{ field }} Must be url',
        ];
    }

    /**
     * Form rule email
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleEmail(string $message = null): array
    {
        return [
            'type'    => 'email',
            'message' => $message ?? '{{ field }} Must be email',
        ];
    }
}