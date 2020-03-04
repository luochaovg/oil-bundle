<?php

namespace Leon\BswBundle\Module\Telegram;

use Telegram\Bot\Commands\Command;

abstract class Acme extends Command
{
    /**
     * @return string
     */
    public function arguments(): string
    {
        return explode(' ', $this->getUpdate()->getMessage()->text)[1] ?? '';
    }
}