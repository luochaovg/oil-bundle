<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;
use Leon\BswBundle\Component\Aes as ComponentAes;
use Respect\Validation\Validator as v;

class Aes extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     * @param array $extraArgs
     *
     * @return mixed
     */
    public function preview($value, array $args, array $extraArgs = [])
    {
        if (empty($extraArgs['aes_iv']) || empty($extraArgs['aes_key']) || empty($value)) {
            return $value;
        }

        $aes = new ComponentAes($extraArgs['aes_iv'], $extraArgs['aes_key']);
        $text = $aes->AESDecode($value);

        $plaintext = $extraArgs['plaintext'] ?? false;
        if (!$plaintext) {
            if (v::phone()->validate($text)) { // phone
                $text = Helper::secretString($text, '*', [strlen($text) => [4, 4]]);
            } elseif (v::email()->validate($text)) { // email
                list($name, $domain) = explode('@', $text);
                $text = Helper::secretString($name) . "@{$domain}";
            } else { // others
                $text = Helper::secretString($text);
            }
        }

        return $text;
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
        if (empty($extraArgs['aes_iv']) || empty($extraArgs['aes_key']) || empty($value)) {
            return $value;
        }

        $aes = new ComponentAes($extraArgs['aes_iv'], $extraArgs['aes_key']);

        return $aes->AESEncode($value);
    }
}