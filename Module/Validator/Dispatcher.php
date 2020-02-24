<?php

namespace Leon\BswBundle\Module\Validator;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\ValidatorException;
use Symfony\Contracts\Translation\TranslatorInterface;

class Dispatcher
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Dispatcher constructor.
     *
     * @param TranslatorInterface $translator
     * @param string              $locale
     */
    public function __construct(TranslatorInterface $translator, string $locale)
    {
        $this->translator = $translator;
        $this->locale = $locale;
    }

    /**
     * Execute
     *
     * @param string $field
     * @param array  $rules
     * @param mixed  $value
     * @param array  $extraArgs
     * @param bool   $sign
     * @param bool   $useLabel
     *
     * @return ValidatorResult
     * @throws
     */
    public function execute(
        string $field,
        array $rules,
        $value,
        array $extraArgs,
        bool $sign,
        bool $useLabel = true
    ): ValidatorResult {

        $result = new ValidatorResult($value, $sign ? $value : false);
        if (empty($rules) && !is_null($value)) {
            return $result;
        }

        $index = 0;
        if ($useLabel) {
            $title = Helper::stringToLabel($field);
            $title = $this->translator->trans($title, [], 'fields');
        } else {
            $title = $field;
        }

        foreach ($rules as $fn => $args) {

            $index += 1;
            if ($index === 1 && $fn === Abs::VALIDATION_IF_SET) {
                if (trim($value) !== '') {
                    continue;
                }
                break;
            }

            if (!class_exists($rule = $fn)) {
                $rule = __NAMESPACE__ . '\\Entity\\' . Helper::underToCamel($fn, false);
            }

            if (!class_exists($rule)) {
                throw new ValidatorException("{$rule} rule don't exists");
            }

            /**
             * @var Validator $validator
             */
            $validator = new $rule(
                $value,
                $args,
                $this->translator,
                $this->locale,
                $extraArgs['_args_handler'][$fn] ?? null
            );

            if (!($validator instanceof Validator)) {
                $className = Validator::class;
                throw new ValidatorException("{$rule} rule must extend class `{$className}`");
            }

            [$result->args, $error] = $validator->validator($extraArgs);
            $value = $result->args;

            if (empty($error)) {
                continue;
            }

            $result->args = false;

            $args = $validator->arrayArgs();
            $error = $this->translator->trans(
                $error,
                [
                    '{{ field }}' => $title,
                    '{{ value }}' => $value,
                    '{{ rule }}'  => $fn,
                    '{{ arg1 }}'  => $args[0] ?? null,
                    '{{ arg2 }}'  => $args[1] ?? null,
                    '{{ arg3 }}'  => $args[2] ?? null,
                    '{{ args }}'  => $validator->stringArgs(),
                ]
            );
            $result->error($error);
        }

        return $result;
    }
}