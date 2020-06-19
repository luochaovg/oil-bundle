<?php

namespace Leon\BswBundle\Module\Bsw;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Module\Entity\Abs;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ArgsInput
{
    /**
     * @var object
     */
    public $cnf;

    /**
     * @var object
     */
    public $usr;

    /**
     * @var string
     */
    public $env;

    /**
     * @var bool
     */
    public $debug;

    /**
     * @var string
     */
    public $route;

    /**
     * @var array
     */
    public $get = [];

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $ctrl;

    /**
     * @var string
     */
    public $cls;

    /**
     * @var string
     */
    public $fn;

    /**
     * @var array
     */
    public $access = [];

    /**
     * @var bool
     */
    public $ajax;

    /**
     * @var bool
     */
    public $iframe;

    /**
     * @var string
     */
    public $abs;

    /**
     * @var string
     */
    public $enum;

    /**
     * @var string
     */
    public $uuid;

    /**
     * @var string
     */
    public $entity;

    /**
     * @var TranslatorInterface
     */
    public $translator;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var Expr
     */
    public $expr;

    /**
     * @var array
     */
    public $render = [];

    /**
     * @var array
     */
    public $args = [];

    /**
     * @var string
     */
    public $scene;

    /**
     * @var array
     */
    public $condition = [];

    /**
     * @var array
     */
    public $data = [];
}