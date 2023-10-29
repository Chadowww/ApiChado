<?php

namespace App\Services;

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
        $this->connectionTest = new PDO(
            'mysql:host=' . $_ENV['APP_DB_HOST'] . '; dbname=' . $_ENV['APP_DB_NAME_TEST'] . '; charset=utf8',
            $_ENV['APP_DB_USER'], $_ENV['APP_DB_PASSWORD']
        );

        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getconnection(): PDO
    {
        return $this->connection;
    }

    public function getConnectionTest(): PDO
    {
        return $this->connectionTest;
    }
}