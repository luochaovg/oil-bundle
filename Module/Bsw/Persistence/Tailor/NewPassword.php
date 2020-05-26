<?php

namespace Leon\BswBundle\Module\Bsw\Persistence\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Input;

class NewPassword extends Tailor
{
    /**
     * @var string
     */
    protected $newField = 'new_password';

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPersistenceAnnotation(Arguments $args): array
    {
        $sort = $args->persistAnnotation[$this->fieldCamel]['sort'] + .01;
        $args->target[$this->newField] = [
            'sort'     => $sort,
            'column'   => 8,
            'type'     => Input::class,
            'typeArgs' => ['type' => Input::TYPE_PASSWORD],
            'tips'     => 'Do not fill if not need',
        ];

        if (empty($args->id)) {
            $args->target[$this->newField]['rules'][] = Abs::RULES_REQUIRED;
        }

        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return Error|array
     */
    public function tailorPersistenceAfterSubmit(Arguments $args)
    {
        $submit = $args->target['submit'];
        $extraSubmit = $args->target['extraSubmit'];

        $newPassword = Helper::dig($extraSubmit, $this->newField);

        if (isset($newPassword) && strlen($newPassword) > 0) {
            $result = $this->web->validator($this->newField, $newPassword, ['password']);
            if ($result === false) {
                return new ErrorParameter($this->web->pop());
            }

            $salt = $submit["{$this->field}Salt"] ?? null;
            $extraSubmit[$this->fieldCamel] = $this->web->password($newPassword, $salt);
        }

        return [$submit, $extraSubmit];
    }
}