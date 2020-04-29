<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Bsw\Crumbs\Entity\Crumb;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAccess;
use Leon\BswBundle\Module\Error\Entity\ErrorAjaxRequest;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Entity\ErrorSession;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Controller\Traits as CT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Exception;
use Throwable;

abstract class BswWebController extends AbstractController
{
    use CT\Foundation,
        CT\WebAccess,
        CT\WebCrumbs,
        CT\WebResponse,
        CT\WebSeo,
        CT\WebSession,
        CT\WebSource;

    /**
     * @var string
     */
    protected $skUser = 'user-session-key';

    /**
     * @var string
     */
    protected $skCaptcha = 'captcha-session-key';

    /**
     * @var bool
     */
    protected $ajax;

    /**
     * @var array
     */
    public $langMap = ['en' => 'en', 'cn' => 'cn'];

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        $this->ajax = $this->request()->isXmlHttpRequest();

        // history for last time
        $args = $this->getArgs();
        if (isset($this->route) && !isset($args['iframe'])) {
            $this->sessionArraySet(Abs::TAG_HISTORY, $this->route, $args);
        }
    }

    /**
     * Create redirect url
     *
     * @param string $url
     * @param array  $args
     *
     * @return string
     */
    protected function redirectUrl(string $url = null, array $args = [])
    {
        if ($url && Helper::isUrlAlready($url)) {
            return $url;
        }

        // from crumbs
        $crumbs = count($this->crumbs) - 2;
        if (!$url && ($crumb = $this->crumbs[$crumbs] ?? null)) {

            /**
             * @var Crumb $crumb
             */
            $url = $crumb->getRoute();
        }

        // from default route
        if (!$url) {
            $url = $this->cnf->route_default;
        }

        // prevent route to self
        if ($this->route == $url) {
            $url = $this->cnf->route_login;
        }

        // args
        if (!empty($url)) {
            $args = (array)$this->sessionArrayGet(Abs::TAG_HISTORY, $url, true);
        }

        return $this->url($url, $args);
    }

    /**
     * Sek cookie
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expire
     *
     * @return void
     */
    protected function setCookie(string $key, $value, int $expire = Abs::TIME_HOUR)
    {
        $cookie = new Cookie($key, $value, time() + $expire);
        $this->response->headers->setCookie($cookie);
    }

    /**
     * Response url map
     *
     * @return array
     */
    protected function responseUrlMap(): array
    {
        $reference = ($this->route == $this->cnf->route_default) ? null : $this->reference();

        return [
            ErrorAjaxRequest::CODE   => $reference,
            ErrorParameter::CODE     => $reference,
            ErrorAuthorization::CODE => $this->cnf->route_login,
            ErrorAccess::CODE        => $reference,
            ErrorSession::CODE       => $this->cnf->route_login,
        ];
    }

    /**
     * Response success (auto ajax)
     *
     * @param string $message
     * @param array  $args
     * @param string $url
     *
     * @return Response
     */
    protected function responseSuccess(string $message, array $args = [], string $url = null): Response
    {
        if ($this->ajax) {
            return $this->successAjax($message, $args);
        }

        [$params, $trans] = $this->resolveArgs($args);
        $message = $this->translator->trans($message, $trans);
        $url = $this->redirectUrl($url);

        return $this->responseMessage($message, $url, $params, Abs::TAG_CLASSIFY_SUCCESS);
    }

    /**
     * Response error (auto ajax)
     *
     * @param int|Error $code
     * @param string    $message
     * @param array     $args
     * @param string    $url
     *
     * @return Response
     */
    protected function responseError($code, string $message = '', array $args = [], string $url = null): Response
    {
        [$code4logic, $tiny, $detail] = [$code, null, null];

        // instance of Error
        if ($code instanceof Error) {
            [$_, $code4logic, $tiny, $detail] = $code->all();
        }

        if ($this->ajax) {
            return $this->failedAjax($code, $message, $args);
        }

        $message && $tiny = $detail = $message;

        // lang for tiny
        [$params, $trans] = $this->resolveArgs($args);
        if (($tiny && $this->langErrorTiny) || $message) {
            $tiny = $this->translator->trans($tiny, $trans);
        }

        // logger description
        if ($detail) {
            $detail = $this->translator->trans($detail, $trans);
            $this->logger->warning("Response error, [{$code4logic}] {$tiny}, {$detail}");
        }

        // fallback url
        $this->session->set(Abs::TAG_FALLBACK, $this->currentUrl());

        // redirect url
        $url = $url ?? ($this->responseUrlMap()[$code4logic] ?? null);
        $message = "[{$code4logic}] {$tiny}";

        return $this->responseMessage($message, $url, $params, Abs::TAG_CLASSIFY_ERROR);
    }

    /**
     * Response message (just latest message)
     *
     * @param string $content
     * @param string $url
     * @param array  $args
     * @param string $classify
     * @param string $type
     * @param int    $duration
     *
     * @return Response
     * @throws
     */
    protected function responseMessage(
        string $content,
        ?string $url = null,
        array $args = [],
        string $classify = Abs::TAG_CLASSIFY_WARNING,
        string $type = Abs::TAG_TYPE_MESSAGE,
        ?int $duration = null
    ): Response {

        [$params, $trans] = $this->resolveArgs($args);

        $content = $this->translator->trans($content, $trans);
        $this->appendMessage($content, $duration, $classify, $type);
        $url = $this->redirectUrl($url, $params);

        return $this->redirect($url);
    }

    /**
     * Response message with ajax (just latest message)
     *
     * @param int|Error $code
     * @param string    $content
     * @param string    $url
     * @param array     $args
     * @param string    $classify
     * @param string    $type
     * @param int       $duration
     *
     * @return Response
     * @throws
     */
    protected function responseMessageWithAjax(
        $code,
        string $content,
        ?string $url = null,
        array $args = [],
        string $classify = Abs::TAG_CLASSIFY_WARNING,
        string $type = Abs::TAG_TYPE_MESSAGE,
        ?int $duration = null
    ): Response {

        [$params, $trans] = $this->resolveArgs($args);
        $content = $this->translator->trans($content, $trans);

        if (isset($url)) {
            $this->appendMessage($content, $duration, $classify, $type);
            $message = null;
            $data = ['href' => $this->redirectUrl($url ?: null, $params)];
        } else {
            $message = $content;
            $data = [];
        }

        [$code4http, $code4logic] = [Response::HTTP_OK, $code, null, null];

        // instance of Error
        if ($code instanceof Error) {
            [$code4http, $code4logic] = $code->all();
        }

        return $this->responseAjax(
            $code4logic,
            $code4http,
            $message,
            $data,
            $classify,
            $type,
            $duration
        );
    }

    /**
     * Append message
     *
     * @param string $content
     * @param int    $duration
     * @param string $classify
     * @param string $type
     *
     * @throws
     */
    protected function appendMessage(
        string $content,
        int $duration = null,
        string $classify = Abs::TAG_CLASSIFY_WARNING,
        string $type = Abs::TAG_TYPE_MESSAGE
    ) {

        if (strpos($content, Abs::TAG_SQL_ERROR) !== false) {
            throw new Exception($content);
        }

        $content = $this->translator->trans($content);
        $content = str_replace(['"', "'"], null, $content);

        $message = [
            'type'     => $type,
            'duration' => $duration,
            'classify' => $classify,
            'content'  => Html::cleanHtml($content),
        ];

        // message to flash
        $this->addFlash(Abs::TAG_MESSAGE, json_encode($message, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Append tips
     *
     * @param array $modalOptions
     */
    protected function appendTips(array $modalOptions)
    {
        $modalOptions = array_merge(
            [
                'title' => 'Tips',
                'width' => '420px',
            ],
            $modalOptions
        );

        foreach (['title', 'content'] as $key) {
            if (!isset($modalOptions[$key])) {
                continue;
            }

            $content = str_replace(['"', "'"], null, $modalOptions[$key]);
            $modalOptions[$key] = Html::cleanHtml($content);
        }

        // message to flash
        $this->addFlash(Abs::TAG_TIPS, json_encode($modalOptions, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Get latest message
     *
     * @param string $key
     * @param bool   $jsonDecode
     *
     * @return mixed
     */
    protected function latestMessage(string $key, bool $jsonDecode = false)
    {
        $list = $this->session->getFlashBag()->get($key);
        $latest = end($list);

        if (!$latest) {
            return $jsonDecode ? [] : null;
        }

        return $jsonDecode ? json_decode($latest, true) : $latest;
    }

    /**
     * Label with menu
     *
     * @param array  $allMenuDetail
     * @param string $route
     * @param string $methodInfo
     * @param string $classInfo
     *
     * @return string
     */
    protected function labelWithMenu(
        array $allMenuDetail,
        string $route,
        string $methodInfo,
        string $classInfo
    ): string {

        $map = [
            'Preview record'     => 'Preview',
            'Persistence record' => 'Persistence',
        ];

        if (!($menuSet = $allMenuDetail[$route]['info'] ?? null)) {
            $route = str_replace(Abs::TAG_PERSISTENCE, Abs::TAG_PREVIEW, $route);
            $menuSet = $allMenuDetail[$route]['info'] ?? null;
        }

        if (isset($map[$methodInfo])) {
            $split = ['cn' => ''][$this->header->lang] ?? ' ';
            $twig = $menuSet ?? $this->translator->trans($classInfo, [], 'twig');
            $twig = $twig . $split . $this->translator->trans($map[$methodInfo], [], 'twig');
        } else {
            $twig = $menuSet ?? $this->translator->trans($methodInfo, [], 'twig');
        }

        return $twig;
    }

    /**
     * Valid args
     *
     * @param int  $type
     * @param bool $showAllError
     *
     * @return object|Response
     * @throws
     */
    final protected function valid(int $type = Abs::VW_LOGIN_AS, bool $showAllError = false)
    {
        $this->iNeedCost(Abs::BEGIN_VALID);

        if (Helper::bitFlagAssert($type, Abs::V_AJAX) && !$this->ajax) {
            $this->iNeedCost(Abs::END_VALID);

            return $this->responseError(new ErrorAjaxRequest());
        }

        $caller = Helper::backtrace(1, ['class', 'function']);
        $annotation = $this->getInputAnnotation($caller['class'], $caller['function']);

        [$error, $args, $sign, $validator] = $this->parametersValidator($annotation);

        /**
         * show error
         */

        if (!empty($error)) {

            if ($showAllError) {
                $message = array_merge(...array_values($error));
                $message = implode(Abs::ENTER, $message);
                $errorCls = ErrorParameter::class;
            } else {
                $message = current(current($error));
                $errorCls = key($error);
            }

            $this->iNeedCost(Abs::END_VALID);

            return $this->responseError(new $errorCls, $message);
        }

        foreach ($validator as $field => $item) {
            $result = call_user_func_array([$this, $item['validator']], [$item['value'], $args]);
            if ($result instanceof Response) {
                $this->iNeedCost(Abs::END_VALID);

                return $result;
            }
        }

        /**
         * should auth
         */

        if (Helper::bitFlagAssert($type, Abs::V_SHOULD_AUTH)) {

            $isAuth = $this->webShouldAuth($args);

            /**
             * auth failed
             */

            if ($isAuth instanceof Error) {

                if (Helper::bitFlagAssert($type, Abs::V_MUST_AUTH)) {

                    $this->logger->warning($this->translator->trans($isAuth->description()));
                    $this->iNeedCost(Abs::END_VALID);

                    return $this->responseError($isAuth);
                }

            } elseif ($isAuth instanceof Response) {

                return $isAuth;

            } else {

                $this->usr = (object)$isAuth;

                /**
                 * access
                 */

                $this->access = $this->accessBuilder($this->usr);
                $access = $this->access[$this->route] ?? false;

                /**
                 * strict authorization
                 */

                if ($this->usrStrict && Helper::bitFlagAssert($type, Abs::V_STRICT_AUTH)) {
                    $_strict = $this->dispatchMethod(Abs::FN_STRICT_AUTH);

                    if ($_strict !== true) {

                        $error = ($_strict instanceof Error) ? $_strict : new ErrorSession();
                        $this->logger->warning($this->translator->trans($error->description()));
                        $this->iNeedCost(Abs::END_VALID);

                        return $this->responseError($error);
                    }
                }

                // access denied
                if (Helper::bitFlagAssert($type, Abs::V_ACCESS) && $access !== true) {

                    $error = new ErrorAccess();
                    $this->logger->warning($this->translator->trans($error->description()));
                    $this->iNeedCost(Abs::END_VALID);

                    return $this->responseError($error);
                }
            }
        }

        $this->iNeedCost(Abs::END_VALID);

        return (object)$args;
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error|Response
     */
    abstract protected function webShouldAuth(array $args);

    /**
     * Get route of all
     *
     * @param bool $value
     *
     * @return array
     */
    protected function getRouteOfAll(bool $value = true): array
    {
        $routes = array_column($this->getRouteCollection(), 'route');
        $routes = Helper::arrayValuesSetTo($routes, $value, true);

        return $routes;
    }

    /**
     * Get access of all
     *
     * @param bool  $keyByClass
     * @param array $menuAssist
     *
     * @return array
     */
    protected function getAccessOfAll(bool $keyByClass = false, ?array $menuAssist = null): array
    {
        $accessList = [];
        $route = $this->getRouteCollection(true);

        foreach ($route as $class => $item) {

            [$classify, $access] = $this->getAccessControlAnnotation($class);
            foreach ($access as $method => &$target) {

                if (!isset($item[$method])) {
                    continue;
                }

                $route = $item[$method]['route'];
                $target = Helper::objectToArray($target);
                $target['info'] = $item[$method]['desc_fn'];

                if ($keyByClass) {
                    $accessList[$classify][$route] = $target;
                } else {
                    $target['classify'] = $classify;
                    $accessList[$route] = $target;
                }
            }
        }

        if (!$keyByClass) {
            return $accessList;
        }

        $_accessList = [];
        $masterMenuDetail = $menuAssist['masterMenuDetail'] ?? [];
        $slaveMenuDetail = $menuAssist['slaveMenuDetail'] ?? [];
        $masterMenu = $menuAssist['masterMenu'] ?? [];

        foreach ($accessList as $classInfo => $items) {
            foreach ($items as $route => $item) {
                if (!isset($_accessList[$classInfo])) {
                    $_accessList[$classInfo] = [
                        'label' => $classInfo,
                        'items' => [],
                    ];
                }

                $target = &$_accessList[$classInfo]['items'];
                $target[$route] = $item;
                $target[$route]['info'] = $this->labelWithMenu(
                    array_merge($masterMenuDetail, $slaveMenuDetail),
                    $route,
                    $target[$route]['info'],
                    $classInfo
                );

                $menuId = $slaveMenuDetail[$route]['parentMenuId'] ?? -1;
                if (isset($masterMenu[$menuId])) {
                    $_accessList[$classInfo]['label'] = $masterMenu[$menuId]->getValue();
                }
            }
        }

        return $_accessList;
    }

    /**
     * Get args for scaffold view
     *
     * @param array $extra
     *
     * @return array
     */
    protected function displayArgsScaffold(array $extra = []): array
    {
        static $scaffold;

        if (!isset($scaffold)) {
            $json = $this->parameters('json');
            [$cls, $fn] = $this->getMCM('-');
            $getArgs = $this->getArgs();

            $scaffold = [
                'cnf'    => $this->cnf,
                'usr'    => $this->usr,
                'env'    => $this->env,
                'debug'  => $this->debug,
                'route'  => $this->route,
                'get'    => $getArgs,
                'url'    => $this->urlSafe($this->route, $getArgs, 'Scaffold', true),
                'ctrl'   => $this->controller,
                'cls'    => $cls,
                'fn'     => $fn,
                'access' => $this->access,
                'ajax'   => $this->ajax,
                'iframe' => empty($getArgs['iframe']) ? false : true,
                'json'   => $json ? json_encode($json) : null,
                'abs'    => static::$abs,
                'enum'   => static::$enum,
                'uuid'   => $this->uuid,
            ];
        }

        return array_merge($scaffold, $extra);
    }

    /**
     * View string handler
     *
     * @param array       $scaffold
     * @param null|string $view
     *
     * @return string
     */
    public function viewHandler(array $scaffold, ?string $view): string
    {
        $suffix = Abs::TPL_SUFFIX;

        if (!$view) {
            // view handler
            if (method_exists($this, $fn = Abs::FN_BLANK_VIEW)) {
                $view = $this->{$fn}($suffix);
            } else {
                $view = "{$scaffold['cls']}/{$scaffold['fn']}{$suffix}";
            }

        } elseif (strpos($view, $suffix) === false) {
            // just it
            $view .= $suffix;
        }

        return $view;
    }

    /**
     * Get render template
     *
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    public function renderPart(string $view, array $parameters): string
    {
        $parameters['scaffold'] = $this->displayArgsScaffold();
        $view = $this->viewHandler($parameters['scaffold'], $view);

        return $this->renderView($view, $parameters);
    }

    /**
     * Render template
     *
     * @param array  $args
     * @param string $view
     *
     * @return Response|string
     */
    public function show(array $args = [], string $view = null)
    {
        $scaffold = $this->displayArgsScaffold(
            [
                'seo'  => $this->seo(),
                'src'  => $this->source(),
                'msg'  => $this->latestMessage(Abs::TAG_MESSAGE),
                'tips' => $this->latestMessage(Abs::TAG_TIPS),
            ]
        );

        $view = $this->viewHandler($scaffold, $view);

        // arguments
        $params = array_merge($args, ['scaffold' => $scaffold]);

        // params before display
        if (method_exists($this, $fn = Abs::FN_BEFORE_DISPLAY)) {
            $params = $this->{$fn}($params);
        }

        // for debug args
        $this->breakpointDebug(Abs::BK_DISPLAY_ARGS, $view, $params);
        $this->logger->debug("-->> end: $this->route");

        $this->iNeedCost(Abs::END_REQUEST);
        $this->iNeedLogger(Abs::END_REQUEST);

        if ($this->ajax) {
            return $this->renderView($view, $params);
        }

        return $this->render($view, $params, $this->response);
    }

    /**
     * Converts an Exception to a Response
     *
     * @param Request             $request
     * @param Exception|Throwable $exception
     *
     * @return Response
     * @throws
     */
    public function showExceptionAction(Request $request, $exception): Response
    {
        if (!$this->ajax) {
            if ($exception instanceof Throwable) {
                throw $exception;
            }
            throw new Exception($exception->getMessage());
        }

        $message = $this->errorHandler(
            "{$exception->getMessage()} in {$exception->getFile()} line {$exception->getLine()}"
        );

        // default http code
        $code4http = Response::HTTP_INTERNAL_SERVER_ERROR;

        // http exception
        if ($exception instanceof HttpExceptionInterface) {
            $code4http = $exception->getStatusCode();
        }

        return $this->responseAjax(
            ErrorException::CODE,
            $code4http,
            $message,
            [],
            Abs::TAG_CLASSIFY_ERROR,
            Abs::TAG_TYPE_CONFIRM,
            0
        );
    }
}