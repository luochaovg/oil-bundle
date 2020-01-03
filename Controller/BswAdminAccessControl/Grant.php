<?php

namespace Leon\BswBundle\Controller\BswAdminAccessControl;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Repository\BswAdminAccessControlRepository;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Grant
{
    /**
     * @param int $id
     *
     * @return array
     */
    protected function listForm(int $id): array
    {
        $form = $this->getAccessOfRender();
        $access = $this->getAccessOfUserWithRole($id);

        foreach ($form as &$item) {
            $item['value'] = [];
            foreach ($item['enum'] as $route => $info) {
                if (isset($access[$route])) {
                    array_push($item['value'], $route);
                }
            }
        }

        foreach ($form as &$item) {

            /**
             * @var Checkbox $checkbox
             */
            $checkbox = $item['type'];
            $checkbox->setEnum($item['enum']);
            $checkbox->setValue($item['value']);
        }

        return $form;
    }

    /**
     * @param array $form
     *
     * @return string
     */
    protected function listRender(array $form): string
    {
        $render = [];
        foreach ($form as $key => $item) {

            /**
             * @var Checkbox $checkbox
             */
            $checkbox = $item['type'];
            $render[$key] = array_keys($checkbox->getEnum());
        }

        return json_encode($render, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get disabled route
     *
     * @param int $userId
     *
     * @return array
     */
    protected function disabled(int $userId = null): array
    {
        // Access from render
        $accessRender = $this->getAccessOfAll();
        $accessDanger = array_filter(Helper::arrayColumn($accessRender, ['class', 'tips']));

        // Access from current administrator user id
        $accessCurrentAdmin = $this->getAccessOfUserWithRole($this->usr->{$this->cnf->usr_uid});
        $unAccessCurrentAdmin = array_diff(array_keys($accessRender), array_keys($accessCurrentAdmin));
        $unAccessCurrentAdmin = Helper::arrayValuesSetTo($unAccessCurrentAdmin, true, true);

        // Access from other administrators user id
        $accessAlreadyOtherAdmin = $this->getAccessOfRoleByUserId($userId);
        $accessDisabled = array_merge($accessAlreadyOtherAdmin, $unAccessCurrentAdmin);

        // Access from root
        if ($this->root($this->usr)) {
            $accessDisabled = $accessAlreadyOtherAdmin;
        }

        return [$accessDisabled, $accessDanger];
    }

    /**
     * Grant authorization for user
     *
     * @Route("/bsw-admin-access-control/grant/{id}", name="app_bsw_admin_access_control_grant", requirements={"id": "\d+"})
     * @Access(class="danger", tips=Abs::DANGER_ACCESS)
     *
     * @param int $id
     *
     * @return Response
     */
    public function grant(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->appendSrcJs('diy;layout/grant');

        /**
         * Grant role
         *
         * @param array $form
         *
         * @return Message
         */
        $grantRole = function (array $form): Message {

            $id = intval(Helper::dig($form, 'id'));
            $id = $id > 0 ? $id : null;
            $routes = $form ? array_merge(...array_values($form)) : [];

            list($disabled) = $this->disabled($id);
            $routes = array_diff($routes, array_keys($disabled));

            /**
             * @var BswAdminAccessControlRepository $access
             */
            $access = $this->repo(BswAdminAccessControl::class);
            $result = $access->transactional(
                function () use ($access, $id, $routes) {

                    $effect = $access->away(['userId' => $id]);
                    if ($effect === false) {
                        throw new RepositoryException($access->pop());
                    }

                    $_routes = [];
                    foreach ($routes as $route) {
                        $_routes[] = [
                            'userId'    => $id,
                            'routeName' => $route,
                        ];
                    }

                    $effect = $access->newlyMultiple($_routes);
                    if ($effect === false) {
                        throw new RepositoryException($access->pop());
                    }
                }
            );

            if ($result === false) {
                return (new Message('Authorized failed'))
                    ->setClassify(Abs::TAG_CLASSIFY_ERROR);
            }

            return (new Message('Authorized success'))
                ->setClassify(Abs::TAG_CLASSIFY_SUCCESS)
                ->setRoute('app_bsw_admin_user_preview');
        };

        list($disabled, $dress) = $this->disabled($id);

        return $this->showPersistence(
            [
                'id'            => $id,
                'dress'         => $dress,
                'disabled'      => $disabled,
                'disabled_json' => json_encode(array_keys($disabled), JSON_UNESCAPED_UNICODE),
                'handler'       => $grantRole,
                'afterModule'   => [
                    'form'   => function (array $logic) {
                        return $this->listForm($logic['id']);
                    },
                    'render' => function (array $logic) {
                        return $this->listRender($logic['form']);
                    },
                ],
            ],
            [],
            'layout/grant'
        );
    }
}