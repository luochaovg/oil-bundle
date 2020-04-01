<?php

namespace Leon\BswBundle\Module\Telegram;

use Telegram\Bot\Commands\Command;
use Envms\FluentPDO\Query as MysqlClient;
use PDO;

abstract class Acme extends Command
{
    /**
     * @return string
     */
    public function arguments(): string
    {
        return explode(' ', $this->getUpdate()->getMessage()->text)[1] ?? '';
    }

    /**
     * @return MysqlClient
     */
    public function pdo(): MysqlClient
    {
        $cnf = parse_url($_ENV['DATABASE_URL']);
        $dbname = trim($cnf['path'], '/');

        $pdo = new PDO(
            "mysql:dbname={$dbname};host={$cnf['host']};port={$cnf['port']}",
            $cnf['user'],
            $cnf['pass']
        );

        $fluent = new MysqlClient($pdo);
        $fluent->exceptionOnError = true;

        return $fluent;
    }
}