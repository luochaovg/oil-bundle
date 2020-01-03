<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Symfony\Component\HttpFoundation\Session\Session as Sessions;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @property Sessions|SessionInterface $session
 */
trait WebSession
{
    /**
     * Set array item in session
     *
     * @param string $sessionKey
     * @param string $setKey
     * @param mixed  $setValue
     *
     * @return array
     */
    public function sessionArraySet(string $sessionKey, string $setKey, $setValue)
    {
        $origin = $this->session->get($sessionKey, []);
        $origin[$setKey] = $setValue;
        $this->session->set($sessionKey, $origin);

        return $origin;
    }

    /**
     * Get array item in session
     *
     * @param string  $sessionKey
     * @param string  $getKey
     * @param boolean $delete
     *
     * @return mixed
     */
    public function sessionArrayGet(string $sessionKey, string $getKey, bool $delete = false)
    {
        $origin = $this->session->get($sessionKey, []);
        if (!isset($origin[$getKey])) {
            return null;
        }

        if ($delete) {
            $item = Helper::dig($origin, $getKey);
            $this->session->set($sessionKey, $origin);
        } else {
            $item = $origin[$getKey] ?? null;
        }

        return $item;
    }
}