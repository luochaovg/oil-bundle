<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Exception\CurlException;
use Leon\BswBundle\Module\Exception\ServiceException;

class Service
{
    /**
     * @const string
     */
    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_HEAD = 'HEAD';

    /**
     * @const string
     */
    const SCHEME_HTTP  = 'http';
    const SCHEME_HTTPS = 'https';

    /**
     * @var int
     */
    protected $method = self::METHOD_GET;

    /**
     * @var string
     */
    protected $scheme = self::SCHEME_HTTP;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port = 80;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $get = [];

    /**
     * @var array
     */
    protected $header = [];

    /**
     * @var int
     */
    protected $timeout = 30000;

    /**
     * @var bool
     */
    protected $async = false;

    /**
     * @param string $method
     *
     * @return Service
     * @throws
     */
    public function method(string $method): Service
    {
        $this->method = $method;

        return $this->methodChecker();
    }

    /**
     * @return Service
     * @throws
     */
    protected function methodChecker(): Service
    {
        $methods = [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_HEAD,
        ];

        if (!in_array($this->method, $methods)) {
            throw new ServiceException('method must in ' . implode('/', $methods));
        }

        return $this;
    }

    /**
     * @param string $scheme
     *
     * @return Service
     * @throws
     */
    public function scheme(string $scheme): Service
    {
        $this->scheme = $scheme;
        if ($scheme == self::SCHEME_HTTPS) {
            $this->port = 443;
        }

        return $this->schemeChecker();
    }

    /**
     * @return Service
     * @throws
     */
    protected function schemeChecker(): Service
    {
        $scheme = [
            self::SCHEME_HTTP,
            self::SCHEME_HTTPS,
        ];

        if (!in_array($this->scheme, $scheme)) {
            throw new ServiceException('scheme must in ' . implode('/', $scheme));
        }

        return $this;
    }

    /**
     * @param string $host
     *
     * @return Service
     * @throws
     */
    public function host(string $host): Service
    {
        $this->host = trim($host, '/ ');

        return $this->hostChecker();
    }

    /**
     * @param bool $strict
     *
     * @return Service
     * @throws
     */
    protected function hostChecker(bool $strict = false): Service
    {
        if (empty($this->host)) {
            throw new ServiceException('host must be configured');
        }

        if ($strict && strpos($this->host, ':')) {
            throw new ServiceException('host contain special char `:`');
        }

        return $this;
    }

    /**
     * @param int $port
     *
     * @return Service
     * @throws
     */
    public function port(int $port): Service
    {
        $this->port = $port;

        return $this->portChecker();
    }

    /**
     * @return Service
     * @throws
     */
    protected function portChecker(): Service
    {
        if ($this->port < 1 || $this->port > 65535) {
            throw new ServiceException('port must between 1 and 65535');
        }

        return $this;
    }

    /**
     * @param string $path
     *
     * @return Service
     */
    public function path(string $path): Service
    {
        $this->path = trim($path, '/ ');

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Service
     */
    public function header(array $args): Service
    {
        $this->header = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Service
     */
    public function args(array $args): Service
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Service
     */
    public function get(array $args): Service
    {
        $this->get = $args;

        return $this;
    }

    /**
     * @param int $millisecond
     *
     * @return Service
     * @throws
     */
    public function timeout(int $millisecond): Service
    {
        $this->timeout = $millisecond;

        return $this->timeoutChecker();
    }

    /**
     * @return Service
     * @throws
     */
    protected function timeoutChecker(): Service
    {
        if ($this->timeout < 0 || $this->timeout > 300000) {
            throw new ServiceException('timeout must between 1 and 30');
        }

        return $this;
    }

    /**
     * @param bool $async
     *
     * @return Service
     */
    public function async(bool $async): Service
    {
        $this->async = $async;

        return $this;
    }

    /**
     * @return mixed
     * @throws
     */
    public function request()
    {
        $part = parse_url($this->host);
        foreach (['scheme', 'host', 'port'] as $item) {
            !empty($part[$item]) && $this->{$item}($part[$item]);
        }

        $_result = $this
            ->methodChecker()
            ->schemeChecker()
            ->hostChecker()
            ->portChecker()
            ->timeoutChecker()
            ->curl();

        if (!$result = json_decode($_result, true)) {
            throw new ServiceException($_result);
        }

        return $result;
    }

    /**
     * @return mixed
     * @throws
     */
    protected function curl()
    {
        $options = [];

        // https
        if ($this->scheme == self::SCHEME_HTTPS) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        // timeout
        $options[CURLOPT_NOSIGNAL] = true;
        $options[CURLOPT_TIMEOUT_MS] = $this->async ? 100 : $this->timeout;

        // enabled show header
        $options[CURLOPT_HEADER] = false;

        // enabled auto show return info
        $options[CURLOPT_RETURNTRANSFER] = true;

        // connect
        $options[CURLOPT_FRESH_CONNECT] = true;
        $options[CURLOPT_FORBID_REUSE] = true;

        // url
        $portString = ":{$this->port}";
        if ($this->scheme == self::SCHEME_HTTP && $this->port == 80) {
            $portString = null;
        } elseif ($this->scheme == self::SCHEME_HTTPS && $this->port == 443) {
            $portString = null;
        }

        $url = "{$this->scheme}://{$this->host}{$portString}/{$this->path}";
        if ($this->get) {
            $url = Helper::addParamsForUrl($this->get, $url);
        }

        if ($this->method == self::METHOD_GET && $this->args) {
            $url = Helper::addParamsForUrl($this->args, $url);
        }

        $options[CURLOPT_URL] = $url;

        // method
        if ($this->method == self::METHOD_HEAD) { // HEAD
            $options[CURLOPT_NOBODY] = true;
            $options[CURLOPT_HEADER] = true;
        } elseif ($this->method == self::METHOD_POST) { // POST
            $options[CURLOPT_POST] = true;
            $this->args && $options[CURLOPT_POSTFIELDS] = http_build_query($this->args);
        }

        // use method POST
        if ($this->method === self::METHOD_POST) {
            $options[CURLOPT_POST] = true;
            !empty($params) && $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        // header
        $header = [];
        foreach ($this->header as $key => $value) {
            array_push($header, "{$key}: {$value}");
        }
        $options[CURLOPT_HTTPHEADER] = $header;

        // request
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $content = curl_exec($curl);

        if ($content === false) {
            $error = curl_error($curl);
            throw new CurlException("curl error when request {$url}, {$error}");
        }

        return $content;
    }
}