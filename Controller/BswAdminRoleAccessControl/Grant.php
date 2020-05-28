<?php

namespace Leon\BswBundle\Controller\BswAdminRoleAccessControl;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Repository\BswAdminRoleAccessControlRepository;
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
        $access = $this->getAccessOfRole($id);

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

        return Helper::jsonStringify($render);
    }

    /**
     * Grant authorization for role
     *
     * @Route("/bsw-admin-role-access-control/grant/{id}", name="app_bsw_admin_role_access_control_grant", requirements={"id": "\d+"})
     * @Access(class="danger", title=Abs::DANGER_ACCESS)
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
        if ($target = $this->getArgs('target')) {
            $this->appendCrumbs($target, $this->cnf->icon_warning);
        }

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

            /**
             * @var BswAdminRoleAccessControlRepository $access
             */
            $access = $this->repo(BswAdminRoleAccessControl::class);
            $result = $access->transactional(
                function () use ($access, $id, $routes) {

                    $effect = $access->away(['roleId' => $id]);
                    if ($effect === false) {
                        throw new RepositoryException($access->pop());
                    }

                    $_routes = [];
                    foreach ($routes as $route) {
                        $_routes[] = [
                            'roleId'    => $id,
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
                ->setRoute('app_bsw_admin_role_preview');
        };

        $dress = $this->getAccessOfAll();
        $dress = Helper::arrayColumn($dress, ['class', 'title']);
        $dress = array_filter($dress);

        $disabled = [];

        return $this->showPersistence(
            [
                'id'            => $id,
                'dress'         => $dress,
                'disabled'      => $disabled,
                'disabled_json' => Helper::jsonStringify(array_keys($disabled)),
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