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
     * Set session
     *
     * @param string $sessionKey
     * @param mixed  $setValue
     *
     * @return array
     */
    public function sessionSet(string $sessionKey, $setValue)
    {
        $origin = is_array($setValue) ? Helper::jsonStringify($setValue) : $setValue;
        $this->session->set($sessionKey, $origin);

        return $origin;
    }

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
        unset($origin[$setKey]);

        $origin[$setKey] = $setValue;
        $this->session->set($sessionKey, $origin);

        return $origin;
    }

    /**
     * Get session
     *
     * @param string $sessionKey
     * @param bool   $delete
     *
     * @return mixed
     */
    public function sessionGet(string $sessionKey, bool $delete = false)
    {
        $origin = $this->session->get($sessionKey);
        $origin = Helper::parseJsonString($origin);

        if ($delete) {
            $this->session->remove($sessionKey);
        }

        return $origin;
    }

    /**
     * Get array item in session
     *
     * @param string $sessionKey
     * @param string $getKey
     * @param bool   $delete
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