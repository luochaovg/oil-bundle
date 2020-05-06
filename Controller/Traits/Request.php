<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Entity\Abs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @property AbstractController $container
 * @property LoggerInterface    $logger
 */
trait Request
{
    /**
     * Args for $_GET
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function getArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_GET, $appoint, $filterHtml);
    }

    /**
     * Args for $_POST
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function postArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_POST, $appoint, $filterHtml);
    }

    /**
     * Args for $_HEAD
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function headArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_HEAD, $appoint, $filterHtml);
    }

    /**
     * Args for $_GET, $_POST and $_HEAD
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function allArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_ALL, $appoint, $filterHtml);
    }

    /**
     * Get params from request
     *
     * @param string $type
     * @param mixed  $appoint
     * @param bool   $filterHtml
     *
     * @return mixed
     */
    public function args(string $type = Abs::REQ_ALL, $appoint = null, bool $filterHtml = true)
    {
        static $args = [], $argsClean = [];

        if (!isset($args[$type]) || !isset($argsClean[$type])) {

            /**
             * @var $request SfRequest
             */
            $request = $this->request();

            $header = function () use ($request) {
                $header = $request->headers->all();
                foreach ($header as $key => &$item) {
                    $item = current($item);
                }

                return $header;
            };

            switch ($type) {

                case Abs::REQ_HEAD:
                    $args[$type] = $header();
                    break;

                case Abs::REQ_GET:
                case Abs::REQ_DELETE:
                    $args[$type] = $request->query->all();
                    break;

                case Abs::REQ_POST:
                case Abs::REQ_PATCH:
                    $args[$type] = $request->request->all();
                    break;

                case Abs::REQ_ALL:
                    $args[$type] = array_merge($request->request->all(), $request->query->all(), $header());
                    break;

                default:
                    $args[$type] = [];
                    break;
            }

            $argsClean[$type] = Html::cleanArrayHtml($args[$type]);
        }

        return Helper::arrayAppoint($filterHtml ? $argsClean[$type] : $args[$type], $appoint);
    }

    /**
     * Get request record
     *
     * @param bool $jsonEncode
     *
     * @return array|string
     */
    public function requestRecord(bool $jsonEncode = false)
    {
        /**
         * @var $request SfRequest
         */
        $request = $this->request();

        $data = [
            'CHECK'       => [
                'route'  => $this->route,
                'method' => $request->getRealMethod(),
                'uri'    => $this->host() . $request->getRequestUri(),
                'ip'     => $this->getClientIp(),
                'locale' => $request->getLocale(),
            ],
            'SERVER'      => $request->server->all(),
            'FILES'       => $request->files->all(),
            'COOKIE'      => $request->cookies->all(),
            Abs::REQ_GET  => $this->getArgs(),
            Abs::REQ_POST => $this->postArgs(),
            Abs::REQ_HEAD => $this->headArgs(),
        ];

        return $jsonEncode ? Helper::jsonStringify($data) : $data;
    }

    /**
     * Logger warning
     *
     * @param string $message
     * @param array  $args
     */
    public function logWarning(string $message, array $args = [])
    {
        $requestArgs = $this->requestRecord();
        $this->logger->warning($message, $args);

        $this->logger->debug('Args $_CHECK', $requestArgs['CHECK']);
        $this->logger->debug('Args $_FILES', $requestArgs['FILES']);
        $this->logger->debug('Args $_GET', $requestArgs[Abs::REQ_GET]);
        $this->logger->debug('Args $_POST', $requestArgs[Abs::REQ_POST]);
        $this->logger->debug('Args $_HEAD', $requestArgs[Abs::REQ_HEAD]);
    }

    /**
     * Logger error
     *
     * @param string $message
     * @param array  $args
     */
    public function logError(string $message, array $args = [])
    {
        $requestArgs = $this->requestRecord();
        $this->logger->error($message, $args);

        $this->logger->debug('Args $_CHECK', $requestArgs['CHECK']);
        $this->logger->debug('Args $_FILES', $requestArgs['FILES']);
        $this->logger->debug('Args $_GET', $requestArgs[Abs::REQ_GET]);
        $this->logger->debug('Args $_POST', $requestArgs[Abs::REQ_POST]);
        $this->logger->debug('Args $_HEAD', $requestArgs[Abs::REQ_HEAD]);
    }

    /**
     * List route key value pair
     *
     * @param bool $sortByKey
     *
     * @return array
     */
    public function routeKVP(bool $sortByKey = null): array
    {
        $route = $this->getRouteCollection();
        $route = array_column($route, 'uri', 'route');
        if ($sortByKey === true) {
            ksort($route);
        } elseif ($sortByKey === false) {
            asort($route);
        }

        return $route;
    }
}