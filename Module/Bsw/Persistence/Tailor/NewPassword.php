<?php

namespace Leon\BswBundle\Module\Bsw\Persistence\Tailor;

use Leon\BswBundle\Component\Helper;
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
     * @param array $annotationExtra
     * @param array $annotation
     * @param int   $id
     *
     * @return array
     */
    public function tailorPersistenceAnnotation(array $annotationExtra, array $annotation, int $id): array
    {
        $sort = $annotation[$this->fieldCamel]['sort'] + .01;
        $annotationExtra[$this->newField] = [
            'sort'     => $sort,
            'column'   => 8,
            'type'     => Input::class,
            'typeArgs' => ['type' => Input::TYPE_PASSWORD],
            'tips'     => 'Do not fill if not need',
        ];

        if (empty($id)) {
            $annotationExtra[$this->newField]['rules'][] = Abs::RULES_REQUIRED;
        }

        return $annotationExtra;
    }

    /**
     * @param array $submitItems
     * @param int   $id
     *
     * @return Error|array
     */
    public function tailorPersistenceAfterSubmit(array $submitItems, int $id)
    {
        [$submit, $extraSubmit] = $submitItems;
        $newPassword = Helper::dig($submit, $this->newField);

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