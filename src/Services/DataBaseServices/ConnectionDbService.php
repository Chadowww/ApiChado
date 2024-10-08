<?php

namespace App\Services\DataBaseServices;

use PDO;

class ConnectionDbService
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = new PDO(
            'mysql:host=' . $_ENV['APP_DB_HOST'] . '; dbname=' . $_ENV['APP_DB_NAME'] . '; charset=utf8',
            $_ENV['APP_DB_USER'], $_ENV['APP_DB_PASSWORD']
        );
    }

    public function connection(): PDO
    {
        return $this->connection;
    }
}