<?php

namespace Leon\BswBundle\Controller\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property TranslatorInterface $translator
 */
trait WebAccess
{
    /**
     * @var array
     */
    protected $access = [];

    /**
     * Master manager
     *
     * @param object $usr
     *
     * @return bool
     */
    protected function root($usr): bool
    {
        return in_array($usr->{$this->cnf->usr_uid}, $this->parameter('backend_auth_root_ids'));
    }

    /**
     * Access builder
     *
     * @param object $usr
     *
     * @return array
     */
    abstract protected function accessBuilder($usr): array;
}