<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\Traits as CT;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Entity\BswAdminLogin;
use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw as BswModule;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Hook\Entity\Aes;
use Leon\BswBundle\Module\Hook\Entity\Enums;
use Leon\BswBundle\Module\Hook\Entity\Timestamp;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Module\Bsw\Header\Entity\Setting;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;

class BswBackendController extends BswWebController
{
    use CT\BackendEntityHint,
        CT\BackendPreset;

    /**
     * @var array
     */
    protected $bsw;

    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_BACKEND;

    /**
     * @var bool
     */
    protected $bswSrc = true;

    /**
     * @var string
     */
    protected $skUser = 'frontend-user-sk';

    /**
     * @var bool
     */
    protected $langErrorTiny = true;

    /**
     * @var bool
     */
    protected $plaintextSensitive = false;

    /**
     * @var array
     */
    protected $mapCdnSrcCss = [];

    /**
     * @var array
     */
    protected $mapCdnSrcJs = [];

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        if ($this->bswSrc) {
            $lang = $this->langLatest($this->langMap, 'en');
            $this->appendSrcJs(
                [Abs::JS_MOMENT_LANG[$lang], Abs::JS_LANG[$lang], Abs::JS_BSW],
                Abs::POS_TOP,
                '',
                true
            );
            $this->appendSrcCss(
                [Abs::CSS_BSW],
                Abs::POS_TOP,
                '',
                true
            );
        }

        if ($this->env === 'dev') {

            $this->mapCdnSrcCss = [];
            $this->mapCdnSrcJs = [];

            if (isset($this->initialSrcJs[$key = 'ant-d'])) {
                $this->appendSrcJsWithKey($key, Abs::JS_ANT_D_LANG, Abs::POS_TOP);
            }
            if (isset($this->initialSrcJs[$key = 'vue'])) {
                $this->appendSrcJsWithKey($key, Abs::JS_VUE, Abs::POS_TOP);
            }
        }
    }

    /**
     * @param array $cnf
     *
     * @return array
     */
    protected function extraConfig(array $cnf): array
    {
        $pair = array_merge($cnf, $this->getDbConfig('app_database_config'));
        $pair = Helper::numericValues($pair);

        return $pair;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function hookerExtraArgs(array $args = []): array
    {
        return Helper::merge(
            [
                Aes::class       => [
                    'aes_iv'     => $this->parameter('aes_iv'),
                    'aes_key'    => $this->parameter('aes_key'),
                    'aes_method' => $this->parameter('aes_method'),
                    'plaintext'  => $this->plaintextSensitive,
                ],
                Enums::class     => [
                    'trans' => $this->translator,
                ],
                Timestamp::class => [
                    'persistence_newly_empty' => time(),
                ],
            ],
            $args
        );
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error|Response
     */
    protected function webShouldAuth(array $args)
    {
        $user = $this->session->get($this->skUser);
        if (empty($user)) {
            return new ErrorAuthorization();
        }

        $strict = $this->parameter('backend_with_ip_strict');
        $userIp = $user['ip'] ?? false;

        if ($strict && ($this->getClientIp() !== $userIp)) {
            $this->logger->error("Account logged in from another place", $user);

            return new ErrorAuthorization();
        }

        return $user;
    }

    /**
     * Strict login
     *
     * @return bool
     * @throws
     */
    protected function strictAuthorization(): bool
    {
        if (!$this->usr) {
            return false;
        }

        $session = $this->repo(BswAdminUser::class)->lister(
            [
                'limit'  => 1,
                'alias'  => 'u',
                'select' => ['u', 'log.addTime AS lastLoginTime'],
                'join'   => [
                    'log' => [
                        'entity' => BswAdminLogin::class,
                        'left'   => ['u.id'],
                        'right'  => ['log.userId'],
                    ],
                ],
                'where'  => [$this->expr->eq('u.id', ':uid')],
                'args'   => ['uid' => [$this->usr('usr_uid')]],
                'order'  => ['log.id' => Abs::SORT_DESC],
            ]
        );

        $strict = [
            'updateTime'    => $this->usr('usr_update'),
            'lastLoginTime' => $this->usr('usr_login'),
        ];

        if (!$this->parameter('backend_with_login_log')) {
            unset($strict['lastLoginTime']);
        }

        if (!$this->parameter('backend_maintain_alone')) {
            $strict = [];
        }

        foreach ($strict as $from => $to) {
            if ($session[$from] != $to) {
                $this->session->clear();

                return false;
            }
        }

        return true;
    }

    /**
     * Access builder
     *
     * @param object $usr
     *
     * @return array
     */
    protected function accessBuilder($usr): array
    {
        $route = $this->getRouteOfAll(true);
        if ($this->root($usr)) {
            return $route;
        }

        $all = $this->getAccessOfAll();
        $render = Helper::arrayValuesSetTo($all, false);
        $user = $this->getAccessOfUserWithRole($usr->{$this->cnf->usr_uid});

        $access = array_merge($route, $render, $user);

        foreach ($render as $key => $value) {
            if (!isset($all[$key])) {
                continue;
            }
            if ($all[$key]['same']) {
                $access[$key] = $access[$all[$key]['same']];
            }
            if ($all[$key]['join'] === false) {
                $access[$key] = true;
            }
        }

        return $access;
    }

    /**
     * Module header setting
     *
     * @return Setting[]
     */
    public function moduleHeaderSetting(): array
    {
        return [
            new Setting('Switch theme', $this->cnf->icon_theme, 'themeSwitch'),
            new Setting('Switch color weak', $this->cnf->icon_bulb, 'colorWeakSwitch'),
            new Setting('Switch third message', $this->cnf->icon_message, 'thirdMessageSwitch'),
        ];
    }

    /**
     * Module header links
     *
     * @return Links[]
     */
    public function moduleHeaderLinks(): array
    {
        if (!$this->urlSafe($this->cnf->route_clean_frontend)) {
            $this->cnf->route_clean_frontend = false;
        }

        $links = [
            new Links('Clean backend cache', $this->cnf->icon_db, $this->cnf->route_clean_backend),
            new Links('Profile', $this->cnf->icon_profile, $this->cnf->route_profile),
            new Links('Logout', $this->cnf->icon_logout, $this->cnf->route_logout),
        ];

        if ($this->cnf->route_clean_frontend) {
            $links = Helper::arrayInsert(
                $links,
                1,
                [
                    new Links(
                        'Clean frontend cache',
                        $this->cnf->icon_redis,
                        $this->cnf->route_clean_frontend
                    ),
                ]
            );
        }

        return $links;
    }

    /**
     * Render module
     *
     * @param array  $moduleList
     * @param string $view
     * @param array  $logicArgs
     *
     * @return Response
     * @throws
     */
    protected function showModule(array $moduleList, string $view, array $logicArgs = []): Response
    {
        if (empty($logicArgs['scene'])) {
            $logicArgs['scene'] = 'unknown';
        }

        $ajaxShowArgs = [];
        $inputArgs = $this->displayArgsScaffold();
        $globalLogic = Helper::dig($inputArgs, 'logic');
        $showArgs = ['logic' => array_merge((array)$globalLogic, $logicArgs)];

        $extraBswArgs = [
            'expr'       => $this->expr,
            'translator' => $this->translator,
            'logger'     => $this->logger,
        ];

        $bswDispatcher = new BswModule\Dispatcher($this);
        $moduleList = Helper::sortArray($moduleList, 'sort');

        foreach ($moduleList as $module => $extraArgs) {
            /**
             * validator extra
             */
            if (!is_array($extraArgs)) {
                throw new ModuleException('The extra args must be array for ' . $module);
            }

            /**
             * @var Bsw $bsw
             */
            $inputArgs = array_merge($inputArgs, $logicArgs, $extraBswArgs, $extraArgs);
            [$name, $twig, $output, $inputArgs] = $bswDispatcher->execute($module, $inputArgs);

            /**
             * @var BswModule\Message $message
             */
            if ($message = $output['message'] ?? null) {
                return $this->messageToResponse($message);
            }

            if (!$name) {
                continue;
            }

            /**
             * twig args
             */

            $showArgs[$name] = $output;
            $this->bsw[$name] = $output;
            $ajaxShowArgs[$name] = $output;

            if (!$twig) {
                continue;
            }

            /**
             * twig html
             */
            $html = $this->renderPart($twig, array_merge($showArgs, [$name => $output]));

            $showArgs["{$name}Html"] = $html;
            $ajaxShowArgs["{$name}Html"] = $html;
        }

        $logic = &$showArgs['logic'];
        $afterModule = Helper::dig($logic, 'afterModule') ?? [];
        Helper::callReturnType($afterModule, Abs::T_ARRAY, 'Handler after module');

        foreach ($afterModule as $key => $handler) {
            if (!is_callable($handler)) {
                continue;
            }
            $logic[$key] = call_user_func_array($handler, [$logic, $showArgs]);
        }

        if ($this->ajax) {
            $content = $this->show($showArgs, $view);
            $ajaxShowArgs = array_merge($showArgs, $ajaxShowArgs, ['content' => $content]);

            return $this->okayAjax($ajaxShowArgs);
        }

        return $this->show($showArgs, $view);
    }

    /**
     * Render module by simple mode
     *
     * @param array $moduleList
     * @param array $logicArgs
     * @param bool  $directResponseMessage
     *
     * @return Response|Message|array
     * @throws
     */
    public function showModuleSimple(array $moduleList, array $logicArgs = [], bool $directResponseMessage = true)
    {
        $showArgs = ['logic' => $logicArgs];
        $inputArgs = $this->displayArgsScaffold();

        $extraBswArgs = [
            'expr'       => $this->expr,
            'translator' => $this->translator,
            'logger'     => $this->logger,
        ];

        $bswDispatcher = new BswModule\Dispatcher($this);
        foreach ($moduleList as $module => $extraArgs) {

            if (is_numeric($module)) {
                [$module, $extraArgs] = [$extraArgs, []];
            }

            /**
             * validator extra
             */
            if (!is_array($extraArgs)) {
                throw new ModuleException('The extra args must be array for ' . $module);
            }

            $inputArgs = array_merge($inputArgs, $logicArgs, $extraBswArgs, $extraArgs);
            [$name, $twig, $output, $inputArgs] = $bswDispatcher->execute($module, $inputArgs);

            /**
             * @var BswModule\Message $message
             */
            if (($message = $output['message'] ?? null)) {
                return $directResponseMessage ? $this->messageToResponse($message) : $message;
            }

            if ($name) {
                $showArgs[$name] = $output;
            }
        }

        return $showArgs;
    }

    /**
     * Get modules for blank
     *
     * @return array
     */
    protected function blankModule(): array
    {
        return [
            BswModule\Menu\Module::class    => ['sort' => Abs::MODULE_MENU_SORT],
            BswModule\Header\Module::class  => ['sort' => Abs::MODULE_HEADER_SORT],
            BswModule\Crumbs\Module::class  => ['sort' => Abs::MODULE_CRUMBS_SORT, 'crumbs' => $this->crumbs],
            BswModule\Tabs\Module::class    => ['sort' => Abs::MODULE_TABS_SORT],
            BswModule\Welcome\Module::class => ['sort' => Abs::MODULE_WELCOME_SORT],
            BswModule\Operate\Module::class => ['sort' => Abs::MODULE_OPERATE_SORT],
            BswModule\Footer\Module::class  => ['sort' => Abs::MODULE_FOOTER_SORT],
            BswModule\Modal\Module::class   => ['sort' => Abs::MODULE_MODAL_SORT],
        ];
    }

    /**
     * Render blank
     *
     * @param string $view
     * @param array  $args
     * @param array  $moduleList
     *
     * @return Response|array
     * @throws
     */
    protected function showBlank(string $view, array $args = [], array $moduleList = []): Response
    {
        $args['scene'] = 'blank';
        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Filter\Module::class => ['sort' => Abs::MODULE_FILTER_SORT],
            ]
        );

        return $this->showModule($moduleList, $view, $args);
    }

    /**
     * Render preview
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showPreview(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        $args['scene'] = 'preview';
        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Filter\Module::class  => ['sort' => Abs::MODULE_FILTER_SORT],
                BswModule\Preview\Module::class => ['sort' => Abs::MODULE_PREVIEW_SORT],
            ]
        );

        return $this->showModule($moduleList, $view ?? 'layout/preview.html', $args);
    }

    /**
     * Render persistence
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showPersistence(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        if (!isset($args['submit'])) {
            $args['submit'] = $this->postArgs('submit', false) ?? [];
        }

        $args['scene'] = 'persistence';
        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [BswModule\Persistence\Module::class => ['sort' => Abs::MODULE_PERSISTENCE_SORT]]
        );

        return $this->showModule($moduleList, $view ?? 'layout/persistence.html', $args);
    }

    /**
     * Render chart
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showChart(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        $args['scene'] = 'chart';
        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Filter\Module::class => ['sort' => Abs::MODULE_FILTER_SORT],
                BswModule\Chart\Module::class  => ['sort' => Abs::MODULE_CHART_SORT],
            ]
        );

        return $this->showModule($moduleList, $view ?? 'layout/chart.html', $args);
    }

    /**
     * Render away without view
     *
     * @param array $args
     * @param array $relation
     * @param bool  $directResponseMessage
     *
     * @return Response|BswModule\Message|array
     * @throws
     */
    protected function doAway(array $args = [], array $relation = [], bool $directResponseMessage = true)
    {
        $args['relation'] = $relation;

        return $this->showModuleSimple(
            [
                BswModule\Menu\Module::class   => ['sort' => Abs::MODULE_MENU_SORT],
                BswModule\Crumbs\Module::class => ['sort' => Abs::MODULE_CRUMBS_SORT, 'crumbs' => $this->crumbs],
                BswModule\Away\Module::class   => ['sort' => Abs::MODULE_AWAY_SORT],
            ],
            $args,
            $directResponseMessage
        );
    }

    /**
     * Get access of render
     *
     * @return array
     */
    protected function getAccessOfRender(): array
    {
        $access = $this->getAccessOfAll(true, $this->bsw['menu']);
        $annotation = [];

        foreach ($access as $key => $item) {

            $enum = [];
            foreach ($item['items'] as $route => $target) {
                if ($target['join'] === false || $target['same']) {
                    continue;
                }
                $enum[$route] = $target['info'];
            }

            $_key = md5($item['label']);

            if (!isset($annotation[$_key])) {
                $annotation[$_key] = [
                    'label' => $item['label'] ?: 'UnSetDescription',
                    'type'  => new Checkbox(),
                    'enum'  => [],
                    'value' => [],
                ];
            }

            $action = &$annotation[$_key];
            $action['enum'] = array_merge($action['enum'], $enum);
        }

        return $annotation;
    }

    /**
     * Get access of role
     *
     * @param int $roleId
     *
     * @return array
     * @throws
     */
    protected function getAccessOfRole(int $roleId = null): array
    {
        $roleId = $roleId ?? $this->usr('usr_role');
        if (empty($roleId)) {
            return [];
        }

        $role = $this->repo(BswAdminRole::class)->find($roleId);
        if (!$role || $role->state !== Abs::NORMAL) {
            return [];
        }

        $access = $this->repo(BswAdminRoleAccessControl::class)->lister(
            [
                'limit'  => 0,
                'alias'  => 'ac',
                'select' => ['ac.routeName AS route'],
                'where'  => [
                    $this->expr->eq('ac.roleId', ':role'),
                    $this->expr->eq('ac.state', ':state'),
                ],
                'args'   => [
                    'role'  => [$roleId],
                    'state' => [Abs::NORMAL],
                ],
            ]
        );

        $access = array_column($access, 'route');
        $access = Helper::arrayValuesSetTo($access, true, true);

        return $access;
    }

    /**
     * Get access of user
     *
     * @param int $userId
     *
     * @return array
     * @throws
     */
    protected function getAccessOfUser(int $userId = null): array
    {
        $userId = $userId ?? $this->usr('usr_uid');
        if (empty($userId)) {
            return [];
        }

        $access = $this->repo(BswAdminAccessControl::class)->lister(
            [
                'limit'  => 0,
                'alias'  => 'ac',
                'select' => ['ac.routeName AS route'],
                'where'  => [
                    $this->expr->eq('ac.userId', ':user'),
                    $this->expr->eq('ac.state', ':state'),
                ],
                'args'   => [
                    'user'  => [$userId],
                    'state' => [Abs::NORMAL],
                ],
            ]
        );

        $access = array_column($access, 'route');
        $access = Helper::arrayValuesSetTo($access, true, true);

        return $access;
    }

    /**
     * Get access of role by user id
     *
     * @param int $userId
     *
     * @return array
     * @throws
     */
    protected function getAccessOfRoleByUserId(int $userId = null): array
    {
        $userId = $userId ?? $this->usr('usr_uid');
        if (empty($userId)) {
            return [];
        }

        /**
         * @var BswAdminUserRepository $userRepo
         */
        $userRepo = $this->repo(BswAdminUser::class);
        $user = $userRepo->find($userId);

        return $this->getAccessOfRole($user->roleId);
    }

    /**
     * Get access of use with role
     *
     * @param int $userId
     *
     * @return array
     */
    protected function getAccessOfUserWithRole(int $userId = null): array
    {
        $userId = $userId ?? $this->usr('usr_uid');
        if (empty($userId)) {
            return [];
        }

        $role = $this->getAccessOfRoleByUserId($userId);
        $user = $this->getAccessOfUser($userId);

        return array_merge($role, $user);
    }

    /**
     * Route is access
     *
     * @param string|null $route
     *
     * @return mixed
     */
    public function routeIsAccess(string $route = null)
    {
        $route = $route ?? $this->route;
        if (empty($route)) {
            return true;
        }

        return $this->access[$route] ?? false;
    }
}