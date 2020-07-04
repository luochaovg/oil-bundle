<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminAccessControlRepository;
use Leon\BswBundle\Repository\BswAdminRoleAccessControlRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

/**
 * @property Expr $expr
 */
trait Preview
{
    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'roleAccessTotal' => [
                'width'  => 130,
                'align'  => 'center',
                'sort'   => 3.1,
                'render' => Abs::HTML_CODE,
            ],
            'userAccessTotal' => [
                'width'  => 130,
                'align'  => 'center',
                'sort'   => 3.2,
                'render' => Abs::HTML_CODE,
            ],
            Abs::TR_ACT       => ['width' => 234],
        ];
    }

    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminUser::class;
    }

    /**
     * @return array
     */
    public function previewTailor(): array
    {
        return [
            Tailor\AttachmentImage::class => [
                0 => 'avatarAttachmentId',
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Sure', null, $this->cnf->icon_submit_form))
                ->setSelector(Abs::SELECTOR_RADIO)
                ->setClick('fillParentForm')
                ->setScene(Button::SCENE_IFRAME)
                ->setArgs(
                    [
                        'repair'   => $this->getArgs('repair'),
                        'selector' => 'id',
                    ]
                ),

            new Button('New record', 'app_bsw_admin_user_persistence', $this->cnf->icon_newly),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Edit record'))
                ->setRoute('app_bsw_admin_user_persistence')
                ->setArgs(['id' => $args->item['id']]),

            (new Button('Google qr code'))
                ->setType(Button::THEME_DEFAULT)
                ->setRoute('app_bsw_admin_user_google_qr_code')
                ->setClick('showModalAfterRequest')
                ->setArgs(
                    [
                        'width' => 300,
                        'id'    => $args->item['id'],
                    ]
                ),

            (new Button('Grant authorization for user'))
                ->setRoute('app_bsw_admin_access_control_grant')
                ->setType(Button::THEME_DANGER)
                ->setArgs(['id' => $args->item['id'], 'target' => $args->item['name']]),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        static $roleAccessTotal;

        /**
         * @var BswAdminRoleAccessControlRepository $roleAccess
         * @var BswAdminAccessControlRepository     $userAccess
         */
        $roleAccess = $this->repo(BswAdminRoleAccessControl::class);
        $userAccess = $this->repo(BswAdminAccessControl::class);

        if (!isset($roleAccessTotal)) {
            $roleAccessTotal = $roleAccess->lister(
                [
                    'alias'  => 'ac',
                    'select' => ['ac.roleId', 'COUNT(ac) AS total'],
                    'where'  => [$this->expr->eq('ac.state', ':state')],
                    'args'   => ['state' => [Abs::NORMAL]],
                    'group'  => ['ac.roleId'],
                ]
            );
            $roleAccessTotal = array_column($roleAccessTotal, 'total', 'roleId');
        }

        $args->hooked['roleAccessTotal'] = $roleAccessTotal[$args->hooked['roleId']] ?? 0;
        $args->hooked['userAccessTotal'] = $userAccess->count(
            ['userId' => $args->hooked['id'], 'state' => Abs::NORMAL]
        );

        return $args->hooked;
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-user/preview", name="app_bsw_admin_user_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}