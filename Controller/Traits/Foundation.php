<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Annotation\Entity\Input;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Component\MysqlDoc;
use Leon\BswBundle\Entity\BswConfig;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Entity\Enum;
use Leon\BswBundle\Module\Traits as MT;
use Leon\BswBundle\Controller\Traits as CT;
use Leon\BswBundle\Repository\FoundationRepository;
use Predis\Client;
use Doctrine\ORM\Query\Expr;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application as CmdApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Leon\BswBundle\Component\Reflection;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Route as RoutingRoute;
use Leon\BswBundle\Module\Hook\Dispatcher as HookerDispatcher;
use Leon\BswBundle\Module\Filter\Dispatcher as FilterDispatcher;
use Leon\BswBundle\Module\Validator\Dispatcher as ValidatorDispatcher;
use Leon\BswBundle\Component\Aes;
use Leon\BswBundle\Component\Service;
use Leon\BswBundle\Module\Exception\ServiceException;
use InvalidArgumentException;
use ReflectionClass;
use Exception;

/**
 * @property AbstractController $container
 */
trait Foundation
{
    use MT\Init,
        MT\Magic,
        MT\Message;

    use CT\Annotation,
        CT\ApiDocument,
        CT\Breakpoint,
        CT\Database,
        CT\DisCache,
        CT\FormRules,
        CT\Mixed,
        CT\Request,
        CT\Upload;

    //
    // ↓↓ For component ↓↓
    //

    /**
     * @var Session|SessionInterface
     */
    protected $session;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Expr
     */
    protected $expr;

    /**
     * @var Response
     */
    protected $response;

    //
    // ↓↓ For variable ↓↓
    //

    /**
     * @var string
     */
    protected $env;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var array
     */
    protected $cnf;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    public static $abs = Abs::class;

    /**
     * @var string
     */
    public static $enum = Enum::class;

    //
    // ↓↓ For logic ↓↓
    //

    /**
     * @var object
     */
    protected $usr;

    /**
     * @var bool
     */
    protected $usrStrict = true;

    /**
     * @var bool
     */
    protected $validatorUseLabel = true;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var object
     */
    protected $header;

    /**
     * @var array
     */
    protected $headerMap = [
        'time',
        'sign',
        'lang',
        'token',
        'sign-dynamic',
        'sign-close',
        'sign-debug',
        'postman-token' => 'postman',
    ];

    /**
     * Foundation constructor.
     *
     * @param ContainerInterface  $container
     * @param SessionInterface    $session
     * @param TranslatorInterface $translator
     * @param AdapterInterface    $cache
     * @param LoggerInterface     $logger
     */
    public function __construct(
        ContainerInterface $container,
        SessionInterface $session,
        TranslatorInterface $translator,
        AdapterInterface $cache,
        LoggerInterface $logger
    ) {
        if (!$this->container) {
            $this->container = $container;
        }

        $this->session = $session;
        $this->session->start();

        $this->beforeInit();

        $this->kernel = $this->container->get('kernel');
        $this->translator = $translator;
        $this->redis = $this->container->get('snc_redis.default');
        $this->cache = $cache;
        $this->logger = $logger;
        $this->expr = new Expr();
        $this->response = new Response();

        $this->env = $this->kernel->getEnvironment();
        $this->debug = $this->kernel->isDebug();

        $config = $this->parameters('cnf', false);
        $config = $this->dispatchMethod(Abs::FN_EXTRA_CONFIG, $config, [$config]);
        $this->cnf = (object)$config;

        $this->iNeedCost(Abs::BEGIN_CONSTRUCT);

        $this->route = $this->request()->get('_route');
        $this->controller = $this->request()->get('_controller');
        $this->uuid = $this->getArgs('uuid') ?? ('_' . Helper::generateToken(8, 36));

        $args = $this->headArgs();
        $args = Helper::arrayPull($args, $this->headerMap, false, '');
        $this->header = (object)$args;
        $this->header->lang = $this->request()->getLocale();

        $this->iNeedCost(Abs::END_CONSTRUCT);
        $this->iNeedCost(Abs::BEGIN_INIT);

        $this->logger->debug("-->> begin: $this->route");
        $this->init();

        $this->iNeedCost(Abs::END_INIT);
        $this->iNeedCost(Abs::BEGIN_REQUEST);
    }

    /**
     * Logger process
     *
     * @param string $scene
     */
    public function iNeedLogger(string $scene)
    {
        /**
         * development environment
         */

        $message = "{$scene} with route {$this->route}";
        if ($this->debug) {
            $this->logWarning($message);

            return;
        }

        /**
         * production environment and no debug uuid
         */

        $uuid = $this->cnf->debug_uuid ?? time();

        /**
         * production environment and debug uuid
         */

        $userId = (($this->usr->{$this->cnf->usr_uid} ?? null) === $uuid);
        $deviceId = (($this->header->device ?? null) === $uuid);

        if ($userId || $deviceId) {
            $this->logError("{$message} for (UUID: ${uuid})");
        } else {
            $this->logWarning($message);
        }
    }

    /**
     * Logger cost
     *
     * @param string $scene
     */
    public function iNeedCost(string $scene)
    {
        if (!$this->cnf->debug_cost) {
            return;
        }

        [$logger, $cost] = Helper::cost($scene);

        // logger
        $this->logger->debug($logger);

        // logger latest
        if ($scene != Abs::END_REQUEST) {
            return;
        }

        $date = date(Abs::FMT_DAY_SIMPLE);
        $key = "request_cost:{$date}";

        $this->logger->debug("-->> total cost {$cost} in request {$this->route}");

        if (!$this->redis) {
            return;
        }

        $exists = $this->redis->exists($key);

        // times
        $timesKey = "{$this->route}_times";
        $times = $this->redis->hget($key, $timesKey) ?? 0;
        $this->redis->hincrby($key, $timesKey, 1);

        // cost
        $costKey = "{$this->route}_cost";
        $avgCost = $this->redis->hget($key, $costKey);
        $avgCost = intval(($times * $avgCost + $cost) / ++$times);

        $this->redis->hset($key, $costKey, $avgCost);
        if (!$exists) {
            $this->redis->expire($key, Abs::TIME_DAY * 2);
        }
    }

    /**
     * Caching
     *
     * @param callable $callback
     * @param string   $key
     * @param int      $time
     * @param bool     $useCache
     *
     * @return mixed
     * @throws
     */
    public function caching(callable $callback, string $key = null, int $time = null, $useCache = null)
    {
        $rebuilding = function () use ($callback) {
            return call_user_func_array($callback, [$this]);
        };

        if (!($useCache ?? ($this->cnf->cache_enabled ?? false))) {
            return $rebuilding();
        }

        if (empty($key)) {
            $caller = Helper::backtrace(1);
            $key = "{$caller['class']}::{$caller['function']}(" . json_encode($caller['args'] ?? []) . ")";
        }

        $this->logger->debug("Using cache, ({$key})");
        $target = $this->cache->getItem(md5($key));
        $target->expiresAfter($time ?? intval($this->cnf->cache_default_expires ?? 3600));

        if (!$target->isHit()) {
            $this->logger->warning("Cache misses so rebuilding now, ({$key})");
            $this->cache->save($target->set($rebuilding()));
        }

        return $target->get();
    }

    /**
     * Get params
     *
     * @param string $name
     * @param mixed  $default
     * @param bool   $inController
     *
     * @return mixed
     */
    public function parameter(string $name, $default = null, bool $inController = true)
    {
        static $config = [];

        if (isset($config[$name])) {
            return $config[$name];
        }

        try {
            return $config[$name] = ($inController ? $this : $this->container)->getParameter($name);
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Get parameter in order
     *
     * @param array  $names
     * @param bool   $inController
     * @param string $assert
     *
     * @return mixed
     */
    protected function parameterInOrder(array $names, bool $inController, string $assert)
    {
        foreach ($names as $name) {
            $value = $this->parameter($name, null, $inController);
            switch ($assert) {
                case Abs::ASSERT_EMPTY:
                    $assert = empty($value);
                    break;
                case Abs::ASSERT_ISSET:
                    $assert = is_null($value);
                    break;
            }

            if ($assert !== true) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Get parameter in order by empty
     *
     * @param array $names
     * @param bool  $inController
     *
     * @return mixed
     */
    public function parameterInOrderByEmpty(array $names, bool $inController = true)
    {
        return $this->parameterInOrder($names, $inController, Abs::ASSERT_EMPTY);
    }

    /**
     * Get parameter in order by isnull
     *
     * @param array $names
     * @param bool  $inController
     *
     * @return mixed
     */
    public function parameterInOrderByIsset(array $names, bool $inController = true)
    {
        return $this->parameterInOrder($names, $inController, Abs::ASSERT_ISSET);
    }

    /**
     * Get params merge bsw bundle
     *
     * @param string $name
     * @param bool   $inController
     * @param string $bundle
     *
     * @return mixed
     * @throws
     */
    public function parameters(string $name, bool $inController = true, string $bundle = 'bsw')
    {
        $params = $this->parameter($name, null, $inController);
        if (is_scalar($params)) {
            return $params;
        }

        $bundleParams = (array)$this->parameter("{$bundle}_{$name}", null, $inController);

        return Helper::merge($bundleParams, (array)$params);
    }

    /**
     * Validator
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $rules
     * @param array  $option
     *
     * @return mixed|false
     * @throws
     */
    public function validator(string $field, $value, array $rules, array $option = [])
    {
        $original = $this->annotation(Input::class, true);
        $option = array_merge($option, ['field' => $field, 'rules' => $rules]);
        $items = $original->converter([new Input($option)]);

        [$error, $args] = $this->parametersValidator(current($items), [$field => $value]);

        if (empty($error)) {
            return $args[$field];
        }

        $this->push(current(current($error)), Abs::TAG_VALIDATOR);

        return false;
    }

    /**
     * Parameters validator
     *
     * @param array $items
     * @param array $values
     *
     * @return array
     * @throws
     */
    public function parametersValidator(array $items, array $values = null): array
    {
        $errorList = $argsList = $signList = $validatorList = [];
        $valuesClean = $values ? Html::cleanArrayHtml($values) : [];

        $extraArgs = $this->dispatchMethod(Abs::FN_VALIDATOR_ARGS, []);
        $dispatcher = new ValidatorDispatcher($this->translator, $this->header->lang);

        foreach ($items as $item) {

            if (isset($values)) {
                $target = $item->html ? $values : $valuesClean;
                $value = $target[$item->field] ?? null;
            } else {
                $value = $this->args($item->method ?: Abs::REQ_ALL, $item->field, !$item->html);
            }

            if ($item->sign == Abs::AUTO) {
                $item->sign = is_null($value) ? false : true;
            }

            $extraArgs['_args_handler'] = $item->rulesArgsHandler;
            $result = $dispatcher->execute(
                $item->field,
                $item->rules,
                $value,
                $extraArgs,
                $item->sign,
                $this->validatorUseLabel
            );

            if (!empty($result->error)) {
                $_class = $item->error;
                if (!isset($errorList[$_class])) {
                    $errorList[$_class] = [];
                }
                $errorList[$_class] = array_merge($errorList[$_class], $result->error);
            }

            if ($result->args !== false) {
                $argsList[$item->field] = $result->args;
                $extraArgs[$item->field] = $result->args;
            }

            if ($result->sign !== false) {
                $signList[$item->field] = $result->sign;
            }

            if (
                isset($argsList[$item->field]) &&
                ($item->validator && method_exists($this, $item->validator)) &&
                !(isset($item->rules[Abs::VALIDATION_IF_SET]) && empty($value))
            ) {
                $validatorList[$item->field] = [
                    'value'     => $argsList[$item->field],
                    'validator' => $item->validator,
                ];
            }
        }

        return [$errorList, $argsList, $signList, $validatorList];
    }

    /**
     * Get current request
     *
     * @return SfRequest
     */
    public function request(): SfRequest
    {
        return $this->container->get('request_stack')->getCurrentRequest() ?: new SfRequest();
    }

    /**
     * @param int       $indexInForwarded
     * @param SfRequest $request
     *
     * @return string
     */
    public function getClientIp(int $indexInForwarded = null, SfRequest $request = null): ?string
    {
        if (!$request) {
            $request = $this->request();
        }

        $default = $request->getClientIp();
        $forwarded = $request->server->get('HTTP_X_FORWARDED_FOR');
        $forwarded = empty($forwarded) ? [] : Helper::stringToArray($forwarded, true, true, 'trim');

        if (count($forwarded) <= 1) {
            return $request->server->get('HTTP_X_REAL_IP', $default);
        }

        if (is_null($indexInForwarded)) {
            $indexInForwarded = $this->cnf->ip_index_in_forwarded ?? -2;
        }

        if ($indexInForwarded < 0) {
            $indexInForwarded = count($forwarded) + $indexInForwarded;
        }

        return empty($forwarded[$indexInForwarded]) ? $default : $forwarded[$indexInForwarded];
    }

    /**
     * Get host
     *
     * @param string $host
     * @param bool   $schemeNeed
     * @param bool   $portNeed
     *
     * @return string
     */
    public function host(string $host = null, bool $schemeNeed = true, $portNeed = true): string
    {
        $request = $this->request();

        if (!$host) {
            if (empty($this->cnf->host)) {
                $host = $request->getHost();
            } else {
                $host = $this->cnf->host;
            }
        }

        if (!$schemeNeed) {
            $host = str_replace(['http://', 'https://'], null, $host);
            $host = '//' . ltrim($host, '/');
        } else {
            if (strpos($host, 'http') !== 0) {
                $scheme = "{$request->getScheme()}://";
                $host = ltrim($host, '/');
                $host = "{$scheme}{$host}";
            }
        }

        $port = null;
        if ($portNeed && !parse_url($host, PHP_URL_PORT)) {
            $port = $request->getPort();
            $port = (in_array($port, [null, 80, 443]) ? null : ":{$port}");
        }

        return "{$host}{$port}";
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function currentUrl(): string
    {
        return $this->host() . $this->request()->getRequestUri();
    }

    /**
     * Get url
     *
     * @param string $route
     * @param array  $params
     * @param bool   $abs
     *
     * @return string
     */
    public function url(string $route, array $params = [], bool $abs = true): string
    {
        $referenceType = $abs ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;

        return $this->generateUrl($route, $params, $referenceType);
    }

    /**
     * Get url by safe mode
     *
     * @param string      $route
     * @param array       $params
     * @param string|null $scene
     * @param bool        $abs
     *
     * @return string|null
     */
    public function urlSafe(string $route, array $params = [], ?string $scene = null, bool $abs = false): ?string
    {
        try {
            $url = $this->url($route, $params, $abs);
        } catch (Exception $e) {
            $url = null;
            $scene = $scene ? "[{$scene}] " : null;
            $this->logger->warning("{$scene}Create url error: {$e->getMessage()}");
        }

        return $url;
    }

    /**
     * Perfect url
     *
     * @param string $url
     *
     * @return string
     */
    public function perfectUrl(string $url)
    {
        if (empty($url)) {
            return $this->host();
        }

        $url = trim($url, '/');
        if (!Helper::isUrlAlready($url)) {
            $request = $this->request();
            $url = "{$request->getScheme()}://{$url}";
        }

        return "{$url}/";
    }

    /**
     * Get page reference (pre page)
     *
     * @return string
     */
    public function reference()
    {
        $reference = $this->request()->server->get('HTTP_REFERER');

        if (!$reference || strpos($reference, $this->host()) === false) {
            return $this->url($this->cnf->route_default);
        }

        return $reference;
    }

    /**
     * Get app name tag
     *
     * @param string $split
     * @param bool   $lower
     *
     * @return string
     */
    public function app(?string $split = '_', bool $lower = true): string
    {
        $app = $this->cnf->app_name;
        if ($lower) {
            $app = strtolower($app);
        }

        return str_replace(' ', $split, $app);
    }

    /**
     * Get origin Module, Class and Method
     *
     * @return array
     */
    public function getOriginMCM(): array
    {
        [$class, $method] = explode('::', $this->controller);
        [$class, $module] = array_reverse(explode('\\', $class));

        return [$module, $class, $method];
    }

    /**
     * Get Module, Class and Method
     *
     * @param string $split
     *
     * @return array
     */
    public function getMCM(string $split = null): array
    {
        [$module, $class, $method] = $this->getOriginMCM();

        // handler
        $method = Helper::camelToUnder($method, $split);
        $class = Helper::camelToUnder($class, $split);
        $module = Helper::camelToUnder($module, $split);

        // controller
        $controller = ($module == 'controller') ? $class : $module;
        $controller = str_replace("{$split}controller", null, $controller);

        // remove tag
        $find = ["get{$split}", "post{$split}", "delete{$split}", "{$split}action"];
        $method = str_replace($find, null, $method);
        $class = str_replace("{$split}controller", null, $class);

        return [$controller, $method, $class, $module];
    }

    /**
     * Get route collection
     *
     * @param bool $keyByClass
     *
     * @return array
     * @throws
     */
    public function getRouteCollection(bool $keyByClass = false)
    {
        return $this->caching(
            function () use ($keyByClass) {

                /**
                 * @var Router $route
                 */
                $route = $this->container->get('router');

                $routeArr = [];
                $ref = new Reflection();

                /**
                 * Get docs
                 *
                 * @param string $class
                 * @param string $method
                 *
                 * @return array
                 */
                $getDoc = function (string $class, string $method) use ($ref) {

                    static $clsDocs = [];
                    static $fnDocs = [];

                    if (!isset($clsDocs[$class])) {
                        $clsDocs[$class] = $ref->getClsDoc($class);
                    }

                    $fn = "{$class}::{$method}";
                    if (!isset($fnDocs[$method])) {
                        $fnDocs[$fn] = $ref->getFnDoc($class, $method);
                    }

                    return [
                        'desc_cls'         => $clsDocs[$class]['info'] ?? null,
                        'desc_fn'          => $fnDocs[$fn]['info'] ?? null,
                        'license'          => $fnDocs[$fn]['license'] ?? [],
                        'license-request'  => $fnDocs[$fn]['license-request'] ?? [],
                        'license-response' => $fnDocs[$fn]['license-response'] ?? [],
                        'property'         => $fnDocs[$fn]['property'] ?? [],
                        'param'            => $fnDocs[$fn]['param'] ?? [],
                        'variable'         => $fnDocs[$fn]['var'] ?? [],
                        'tutorial'         => $fnDocs[$fn]['tutorial'] ?? null,
                    ];
                };

                foreach ($route->getRouteCollection() as $key => $item) {

                    if (strpos($key, '_') === 0) {
                        continue;
                    }

                    /**
                     * @var RoutingRoute $item
                     */
                    $controller = $item->getDefault('_controller');
                    [$class, $method] = explode('::', $controller);

                    $_item = array_merge(
                        [
                            'route'  => $key,
                            'uri'    => $item->getPath(),
                            'http'   => $item->getMethods(),
                            'app'    => Helper::cutString($class, '\\^1^desc'),
                            'class'  => $class,
                            'method' => $method,
                        ],
                        $getDoc($class, $method)
                    );

                    if ($keyByClass) {
                        $routeArr[$class][$method] = $_item;
                    } else {
                        $routeArr[$controller] = $_item;
                    }
                }

                return $routeArr;
            }
        );
    }

    /**
     * Get component instance
     *
     * @param string $class
     * @param bool   $single
     *
     * @return object
     * @throws
     */
    public function component(string $class, bool $single = true)
    {
        static $instance = [];

        if (isset($instance[$class]) && $single) {
            return $instance[$class];
        }

        $component = $this->parameters('component') ?? [];
        if (!isset($component[$class])) {
            throw new InvalidArgumentException("Component config not exists `{$class}`");
        }

        $component = $component[$class];
        if (!class_exists($class)) {
            if (!isset($component['class'])) {
                throw new InvalidArgumentException("Component class not exists `{$class}`");
            }
            $class = $component['class'];
        }

        if (!isset($component['arguments']) || !is_array($component['arguments'])) {
            throw new InvalidArgumentException("Component arguments not setting or not array `{$class}`");
        }

        $reflection = new ReflectionClass($class);
        $parameters = $reflection->getConstructor()->getParameters();
        $arguments = $component['arguments'];

        if (Helper::typeofArray($arguments, Abs::T_ARRAY_INDEX)) {
            return $instance[$class] = $reflection->newInstanceArgs($arguments);
        }

        $args = [];
        foreach ($parameters as $i) {
            $args[$i->name] = $arguments[$i->name] ?? ($i->isDefaultValueAvailable() ? $i->getDefaultValue() : null);
        }

        return $instance[$class] = $reflection->newInstanceArgs($args);
    }

    /**
     * @param array        $hooks
     * @param object|array $item
     * @param bool         $persistence
     * @param callable     $before
     * @param callable     $after
     * @param array        $extraArgs
     *
     * @return object|array
     * @throws
     */
    public function hooker(
        array $hooks,
        $item,
        bool $persistence = false,
        callable $before = null,
        callable $after = null,
        array $extraArgs = []
    ) {

        if (empty($item)) {
            return $item;
        }

        $more = is_array($item) && Helper::typeofArray($item, Abs::T_ARRAY_INDEX);
        if (!$more) {
            $item = [$item];
        }

        $hooker = new HookerDispatcher();

        $_extraArgs = $this->dispatchMethod(Abs::FN_HOOKER_ARGS, []);
        $extraArgs = Helper::merge($_extraArgs, $extraArgs);

        $item = $hooker
            ->setHooks($hooks)
            ->setBeforeHandler($before)
            ->setAfterHandler($after)
            ->executeAny($item, $persistence, $extraArgs);

        return $more ? $item : current($item);
    }

    /**
     * Get filter
     *
     * @param array $condition
     * @param int   $mode
     * @param bool  $append
     * @param array $fieldMap
     *
     * @return array
     */
    public function filter(
        array $condition,
        int $mode = FilterDispatcher::DQL_MODE,
        bool $append = false,
        array $fieldMap = []
    ): array {

        $filterDispatcher = new FilterDispatcher();
        $query = $filterDispatcher->filterList($condition, $mode, $append, $fieldMap);

        $filter = [
            'where' => [],
            'args'  => [],
        ];

        if ($mode === FilterDispatcher::SQL_MODE) {
            [$filter['where'], $filter['args']] = $query;

            return $filter;
        }

        foreach ($query as $item) {
            [$where, $args, $type] = $item;
            $filter['where'][] = $where;
            foreach ($args as $k => $arg) {
                $filter['args'][$k] = [$arg, $type[$k]];
            }
        }

        return $filter;
    }

    /**
     * Handle error with diff env
     *
     * @param Exception|string $error
     *
     * @return string
     */
    public function errorHandler($error): ?string
    {
        if ($error instanceof Exception) {
            $error = "{$error->getMessage()} in {$error->getFile()} line {$error->getLine()}";
        }

        if (!is_string($error)) {
            return null;
        }

        if ($this->debug) {
            $this->logger->error("Unforeseen error, {$error}");

            return $error;
        }

        $code = Helper::strPadLeftLength(rand(1, 9999), 4);
        $code = date('md') . $code;

        $this->logger->error("Unforeseen error, [{$code}] {$error}");

        return $this->translator->trans('[{{ code }}] Unforeseen error', ['{{ code }}' => $code]);
    }

    /**
     * Call service api
     *
     * @param string $path
     * @param array  $args
     * @param string $method
     *
     * @return object
     * @throws
     */
    public function service(string $path, array $args = [], string $method = Abs::REQ_GET)
    {
        $serviceHost = $args['service_host'] ?? $this->cnf->service_host;
        $serviceSalt = $args['service_salt'] ?? $this->parameter('service_salt');
        $path = str_replace('.', '/', trim($path, '/.'));

        if (empty($serviceHost)) {
            throw new Exception("Service host is required when call `{$path}`");
        }

        if (empty($serviceSalt)) {
            throw new Exception("Service salt is required when call `{$path}`");
        }

        $host = rtrim($serviceHost, '/');
        $header = Helper::signature($args, $serviceSalt);
        $header['lang'] = $this->request()->getLocale();

        $server = new Service();
        $server->host($host);
        $server->path($path);
        $server->header($header);
        $server->args($args);
        $server->method($method);

        try {
            $result = $server->request();
        } catch (ServiceException $e) {

            /**
             * @var Aes $aes
             */
            $aes = $this->component('AesService');

            $aesResult = $e->getMessage();
            $jsonResult = $aes->AESDecode($aesResult) ?: $aesResult;
            $result = Helper::parseJsonString($jsonResult) ?? $jsonResult;

            if (!is_array($result)) {
                throw new ServiceException("Service caller {$host}/{$path} got exception\n\n{$result}");
            }
        }

        if (!empty($result['error'])) {
            throw new ServiceException(
                "Service caller {$host}/{$path} got error\n\n[{$result['error']}] {$result['message']}"
            );
        }

        return (object)$result;
    }

    /**
     * Encrypt password
     *
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public function password(string $password, string $salt = null): string
    {
        $salt = $salt ?? $this->parameter('salt');

        if ($password) {
            $password = md5(strrev($salt)) . md5($password) . md5($salt);
            $password = md5($password);
        }

        return $password;
    }

    /**
     * Latest lang
     *
     * @param array  $map
     * @param string $defaultLang
     *
     * @return string
     */
    public function langLatest(array $map = [], string $defaultLang = null): string
    {
        $lang = Enum::LANG_TO_LOCALE[$this->header->lang] ?? 'en';

        if (isset($map[$lang])) {
            return $map[$lang];
        }

        if ($defaultLang && isset($map[$defaultLang])) {
            return $map[$defaultLang];
        }

        return $lang;
    }

    /**
     * Fields with lang
     *
     * @param array $fields
     * @param bool  $camel
     * @param array $map
     *
     * @return array
     */
    public function langFields(array $fields, bool $camel = true, array $map = []): array
    {
        $lang = $this->langLatest($map, 'en');

        return Helper::arrayMap(
            $fields,
            function ($item) use ($lang, $camel) {
                $field = "{$lang}_{$item}";

                return $camel ? Helper::underToCamel($field) : $field;
            }
        );
    }

    /**
     * Filter for lang
     *
     * @param string $alias
     * @param bool   $zeroNeed
     * @param array  $map
     *
     * @return array
     */
    public function langFilter(string $alias, bool $zeroNeed = true, array $map = Enum::LANG): array
    {
        $index = $map[$this->header->lang] ?? 0;

        if ($zeroNeed) {
            $index = array_unique([0, $index]);
            $index = (count($index) === 1 ? current($index) : $index);
        }

        if (is_array($index)) {
            $filter = ['where' => [$this->expr->in("{$alias}.lang", $index)]];
        } else {
            $filter = [
                'where' => [$this->expr->eq("{$alias}.lang", ':lang')],
                'args'  => ['lang' => [$index]],
            ];
        }

        return $filter;
    }

    /**
     * Get location with lang
     *
     * @param string $ip
     * @param bool   $returnArray
     * @param array  $map
     *
     * @return string|array
     */
    public function locationWithLang(
        string $ip,
        bool $returnArray = false,
        array $map = ['cn' => 'CN', 'en' => 'EN']
    ) {

        $lang = $this->langLatest($map, 'en');
        $location = $this->ip2regionIPDB($ip, 'ip2region.ipip.ipdb', $lang)['location'];
        $location = array_filter(explode('|', $location));

        return $returnArray ? $location : implode(' ', $location);
    }

    /**
     * Encode the enum
     *
     * @param array $enum
     *
     * @return string
     */
    public function enumEncode(array $enum): string
    {
        return json_encode($enum, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Lang the enum
     *
     * @param array $enum
     * @param bool  $encode
     *
     * @return array|string
     */
    public function enumLang(array $enum, bool $encode = false)
    {
        foreach ($enum as &$label) {
            $label = $this->translator->trans($label, [], 'enum');
        }

        return $encode ? $this->enumEncode($enum) : $enum;
    }

    /**
     * Lang the label
     *
     * @param string $label
     *
     * @return string
     */
    public function labelLang(string $label): string
    {
        return $this->translator->trans($label, [], 'fields');
    }

    /**
     * Get db config
     *
     * @param string $key
     *
     * @return array
     */
    public function getDbConfig(string $key): array
    {
        $args = function (string $key) {
            return $this->parameter($key, null, false);
        };

        return $this->caching(
            function () use (&$args) {

                /**
                 * @var FoundationRepository $repo
                 */
                $repo = $this->repo(BswConfig::class);

                return $repo->kvp(['value'], 'key');
            },
            $key,
            $args('db_cache_default_expires'),
            $args('db_cache_enabled')
        );
    }

    /**
     * Mysql scheme document
     *
     * @param string $table
     * @param string $doctrine
     *
     * @return array
     */
    public function mysqlSchemeDocument(string $table = null, string $doctrine = null): array
    {
        $pdo = $this->pdo($doctrine ?? Abs::DOCTRINE_DEFAULT);
        $database = $pdo->getDatabase();
        $document = (new MysqlDoc())->create($pdo, [$database]);
        $document = $document[$database] ?? [];

        return $table ? ($document[$table] ?? []) : $document;
    }

    /**
     * Manual list for pagination
     *
     * @param array $list
     * @param array $query
     *
     * @return array
     * @throws
     */
    public function manualListForPagination(array $list, array $query): array
    {
        if (!$query['paging']) {
            return $list;
        }

        $query = array_merge($query, Helper::pageArgs($query, FoundationRepository::PAGE_SIZE));
        $total = count($list);

        $query['limit'] = $query['limit'] ?: $total;

        return [
            Abs::PG_CURRENT_PAGE => $query['page'],
            Abs::PG_PAGE_SIZE    => $query['limit'],
            Abs::PG_TOTAL_PAGE   => ceil($total / $query['limit']),
            Abs::PG_TOTAL_ITEM   => $total,
            Abs::PG_ITEMS        => array_slice($list, $query['offset'], $query['limit']),
        ];
    }

    /**
     * Call an command
     *
     * @param string $command
     * @param array  $condition
     *
     * @return string
     * @throws
     */
    public function commandCaller(string $command, array $condition = []): string
    {
        $application = new CmdApplication($this->kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();
        $application->find($command)->run(new ArrayInput($condition), $output);

        return $output->fetch();
    }
}