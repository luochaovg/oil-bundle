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

    public function pdo() {
        $pdo = new PDO(
            "mysql:dbname={$cnf['dbname']};host={$cnf['host']};port={$cnf['port']}",
            $cnf['user'],
            $cnf['password']
        );

        $fluent = new Query($pdo);
        $fluent->exceptionOnError = true;
    }
}