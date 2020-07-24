<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Hook\Hook;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessagesTrans extends Hook
{
    /**
     * @const string
     */
    const DOMAIN = 'messages';

    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function preview($value, array $args, array $extraArgs = [])
    {
        $trans = $extraArgs['trans'] ?? null;
        if (is_object($trans) && $trans instanceof TranslatorInterface) {
            return $trans->trans($value, [], static::DOMAIN);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function persistence($value, array $args, array $extraArgs = [])
    {
        return $value;
    }
}