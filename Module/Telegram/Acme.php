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
        $config = parse_url($_ENV['DATABASE_URL']);
        $dbname = trim($config['path'], '/');

        $pdo = new PDO(
            "mysql:dbname={$dbname};host={$config['host']};port={$config['port']}",
            $config['user'],
            $config['pass']
        );

        $fluent = new MysqlClient($pdo);
        $fluent->exceptionOnError = true;

        return $fluent;
    }
}