<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Entity\BswAdminUser;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;

trait Profile
{
    /**
     * @return string
     */
    public function profileEntity(): string
    {
        return BswAdminUser::class;
    }

    /**
     * @return array
     */
    public function profileTailor(): array
    {
        return [
            Tailor\NewPassword::class => [
                0 => 'password',
            ],
        ];
    }

    /**
     * @return array
     */
    public function profileAnnotation(): array
    {
        return [
            'phone'  => ['disabled' => true],
            'roleId' => false,
            'state'  => false,
        ];
    }

    /**
     * User profile
     *
     * @Route("/user/profile", name="app_user_profile")
     *
     * @return Response
     */
    public function profile(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $this->usr->{$this->cnf->usr_uid}]);
    }
}