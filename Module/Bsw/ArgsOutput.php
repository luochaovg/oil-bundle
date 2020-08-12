<?php

namespace Leon\BswBundle\Module\Bsw;

class ArgsOutput
{
    /**
     * @var Message
     */
    public $message;

    /**
     * ArgsOutput constructor.
     *
     * @param ArgsInput $input
     */
    public function __construct(ArgsInput $input = null)
    {
        if (!$input) {
            return;
        }

        foreach ($this as $attribute => $value) {
            if (is_null($value) && property_exists($input, $attribute)) {
                $this->{$attribute} = $input->{$attribute};
            }
        }
    }
}