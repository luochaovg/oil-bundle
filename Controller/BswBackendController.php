<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\Traits as CT;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Entity\BswAdminLogin;
use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw as BswModule;
use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
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
use Exception;

class BswBackendController extends BswWebController
{
    use CT\EntityHint,
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
     * @var string
     */
    protected $layoutBsw = 'layout/blank';

    /**
     * @var string
     */
    protected $layoutDiy = 'layout/empty';

    /**
     * @var bool
     */
    protected $plaintextSensitive = false;

    /**
     * @var array
     */
    protected $mapCdnSrcCss = [
        // 'ant-d'   => 'https://cdn.jsdelivr.net/npm/ant-design-vue@1.3.16/dist/antd.min.css',
        // 'animate' => 'https://cdn.jsdelivr.net/npm/animate.css@3.7.2/animate.min.css',
    ];

    /**
     * @var array
     */
    protected $mapCdnSrcJs = [
        // 'jquery'   => 'https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js',
        // 'moment'   => 'https://cdn.jsdelivr.net/npm/moment@2.24.0/min/moment.min.js',
        // 'vue'      => 'https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js',
        // 'ant-d'    => 'https://cdn.jsdelivr.net/npm/ant-design-vue@1.3.16/dist/antd.min.js',
        // 'rsa'      => 'https://cdn.jsdelivr.net/npm/jsencrypt@3.0.0-rc.1/bin/jsencrypt.min.js',
        // 'e-charts' => 'https://cdn.jsdelivr.net/npm/echarts@4.3.0/dist/echarts.min.js',
    ];

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        if ($this->bswSrc) {
            $lang = $this->langLatest(['en' => 'en', 'cn' => 'cn']);
            $this->appendSrcJs([Abs::JS_MOMENT_LANG[$lang], Abs::JS_LANG[$lang], Abs::JS_BSW]);
            $this->appendSrcCss(Abs::CSS_BSW);
        }

        if ($this->env === 'dev') {

            $this->mapCdnSrcCss = [];
            $this->mapCdnSrcJs = [];

            if (isset($this->initialSrcJs[$key = 'ant-d'])) {
                $this->appendSrcJsWithKey($key, Abs::JS_ANT_D_LANG);
            }
            if (isset($this->initialSrcJs[$key = 'vue'])) {
                $this->appendSrcJsWithKey($key, Abs::JS_VUE);
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
                    'aes_iv'    => $this->parameter('aes_iv'),
                    'aes_key'   => $this->parameter('aes_key'),
                    'plaintext' => $this->plaintextSensitive,
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
                'args'   => ['uid' => [$this->usr->{$this->cnf->usr_uid}]],
                'order'  => ['log.id' => Abs::SORT_DESC],
            ]
        );

        $strict = [
            'updateTime'    => $this->usr->{$this->cnf->usr_update},
            'lastLoginTime' => $this->usr->{$this->cnf->usr_login} ?? null,
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
     * @param array       $moduleList
     * @param string|null $view
     * @param array       $logicArgs
     * @param bool        $returnLatestModule
     *
     * @return Response|array
     * @throws
     */
    protected function showModule(
        array $moduleList,
        ?string $view = null,
        array $logicArgs = [],
        bool $returnLatestModule = false
    ) {

        $ajaxShowArgs = [];
        $showArgs = [Abs::TAG_LOGIC => $logicArgs];
        $inputArgs = $this->displayArgsScaffold();

        $extraBswArgs = [
            'expr'       => $this->expr,
            'translator' => $this->translator,
            'logger'     => $this->logger,
        ];

        $i = 0;
        $total = count($moduleList);
        $bswDispatcher = new BswModule\Dispatcher($this);

        foreach ($moduleList as $module => $extraArgs) {

            $i += 1;
            if (is_numeric($module)) {
                [$module, $extraArgs] = [$extraArgs, []];
            }

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

            if ($returnLatestModule && $i === $total) {
                return $output;
            }

            /**
             * @var BswModule\Message $message
             */
            if ($message = $output['message'] ?? null) {

                $args = [
                    $message->getMessage(),
                    $message->getRoute(),
                    $message->getArgs(),
                    $message->getClassify(),
                    $message->getType(),
                    $message->getDuration(),
                ];

                if (!$this->ajax) {
                    return $this->responseMessage(...$args);
                }

                $codeMap = [
                    Abs::TAG_CLASSIFY_SUCCESS => $this->codeOkForLogic,
                    Abs::TAG_CLASSIFY_ERROR   => new ErrorException(),
                ];

                $classify = $message->getClassify();
                $code = $codeMap[$classify] ?? $this->codeOkForLogic;

                return $this->responseMessageWithAjax($code, ...$args);
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
            $html = $this->renderPart($twig, [$name => $output]);

            $showArgs["{$name}_html"] = $html;
            $ajaxShowArgs["{$name}_html"] = $html;
        }

        if (empty($view)) {
            return $this->responseMessage('Module has not view for render');
        }

        $logic = &$showArgs[Abs::TAG_LOGIC];
        $afterModule = Helper::dig($logic, 'afterModule');

        if ($afterModule && is_array($afterModule)) {
            foreach ($afterModule as $key => $handler) {
                if (!is_callable($handler)) {
                    $logic[$key] = $handler;
                } else {
                    $logic[$key] = call_user_func_array($handler, [$logic]);
                }
            }
        }

        if ($this->ajax) {
            $content = $this->show($showArgs, $view);
            $ajaxShowArgs = array_merge($showArgs, $ajaxShowArgs, ['content' => $content]);

            return $this->okayAjax($ajaxShowArgs);
        }

        return $this->show($showArgs, $view);
    }

    /**
     * Get modules for blank
     *
     * @return array
     */
    protected function blankModule(): array
    {
        return [
            BswModule\Menu\Module::class,
            BswModule\Header\Module::class,
            BswModule\Crumbs\Module::class => ['crumbs' => $this->crumbs],
            BswModule\Welcome\Module::class,
            BswModule\Operate\Module::class,
            BswModule\Footer\Module::class,
            BswModule\Modal\Module::class,
        ];
    }

    /**
     * Render blank
     *
     * @param array  $args
     * @param array  $moduleList
     * @param string $view
     * @param bool   $returnLatestModule
     *
     * @return Response|array
     * @throws
     */
    protected function showBlank(
        array $args = [],
        array $moduleList = [],
        string $view = 'layout/blank',
        bool $returnLatestModule = false
    ): Response {

        $moduleList = array_merge(
            $this->blankModule(),
            $moduleList,
            [BswModule\Filter\Module::class]
        );

        return $this->showModule($moduleList, $view, $args, $returnLatestModule);
    }

    /**
     * Render preview
     *
     * @param array  $args
     * @param array  $moduleList
     * @param string $view
     * @param bool   $returnLatestModule
     *
     * @return Response|array
     * @throws
     */
    protected function showPreview(
        array $args = [],
        array $moduleList = [],
        string $view = 'layout/preview',
        bool $returnLatestModule = false
    ): Response {

        $moduleList = array_merge(
            $this->blankModule(),
            $moduleList,
            [BswModule\Filter\Module::class, BswModule\Preview\Module::class]
        );

        return $this->showModule($moduleList, $view, $args, $returnLatestModule);
    }

    /**
     * Render persistence
     *
     * @param array  $args
     * @param array  $moduleList
     * @param string $view
     * @param bool   $returnLatestModule
     *
     * @return Response|array
     * @throws
     */
    protected function showPersistence(
        array $args = [],
        array $moduleList = [],
        string $view = 'layout/persistence',
        bool $returnLatestModule = false
    ): Response {

        if (!isset($args['submit'])) {
            $args['submit'] = $this->postArgs('submit', false) ?? [];
        }

        $moduleList = array_merge(
            $this->blankModule(),
            $moduleList,
            [BswModule\Persistence\Module::class]
        );

        return $this->showModule($moduleList, $view, $args, $returnLatestModule);
    }

    /**
     * Get chart option
     *
     * @param array  $option
     * @param string $chart
     * @param string $width
     * @param string $height
     * @param array  $style
     * @param string $theme
     *
     * @return array
     */
    protected function createChartOption(
        array $option,
        string $chart,
        string $width = null,
        string $height = null,
        array $style = null,
        string $theme = Abs::CHART_DEFAULT_THEME
    ) {

        /**
         * @var Chart $chart
         */
        $chart = new $chart($option, Helper::isMobile());

        if (isset($option['beforeOption']) && is_callable($option['beforeOption'])) {
            $chart = call_user_func_array($option['beforeOption'], [$chart]);
        }

        $fullOption = $chart->option();

        if (isset($option['afterOption']) && is_callable($option['afterOption'])) {
            $fullOption = call_user_func_array($option['afterOption'], [$fullOption]);
        }

        $width = is_null($width) ? '92%' : $width;
        $height = is_null($height) ? '700px' : $height;
        $style = is_null($style) ? ['margin' => '50px auto', 'float' => 'none'] : $style;

        $mobile = Helper::isMobile();

        return [
            'width'  => $mobile ? '100%' : $width,
            'height' => $mobile ? '400px' : $height,
            'style'  => $mobile ? null : Html::cssStyleFromArray($style),
            'theme'  => $theme,
            'map'    => $option['map'] ?? null,
            'api'    => $this->urlSafe($this->route, $this->getArgs(), 'Chart api'),
            'option' => json_encode($fullOption, JSON_UNESCAPED_UNICODE) ?: '{}',
        ];
    }

    /**
     * Render chart
     *
     * @param array  $args
     * @param array  $moduleList
     * @param string $view
     * @param bool   $returnLatestModule
     *
     * @return Response|array
     * @throws
     */
    protected function showChart(
        array $args = [],
        array $moduleList = [],
        string $view = 'layout/chart',
        bool $returnLatestModule = false
    ): Response {

        $moduleList = array_merge(
            $this->blankModule(),
            $moduleList,
            [BswModule\Filter\Module::class, BswModule\Chart\Module::class]
        );

        return $this->showModule($moduleList, $view, $args, $returnLatestModule);
    }

    /**
     * Render away without view
     *
     * @param array $args
     * @param array $relation
     * @param bool  $returnLatestModule
     *
     * @return Response|array
     * @throws
     */
    protected function doAway(array $args = [], array $relation = [], bool $returnLatestModule = false)
    {
        $args['relation'] = $relation;

        return $this->showModule([BswModule\Away\Module::class], null, $args, $returnLatestModule);
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
        $roleId = $roleId ?? $this->usr->{$this->cnf->usr_role};
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
        $userId = $userId ?? $this->usr->{$this->cnf->usr_uid};
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
        $userId = $userId ?? $this->usr->{$this->cnf->usr_uid};
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
        $userId = $userId ?? $this->usr->{$this->cnf->usr_uid};
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