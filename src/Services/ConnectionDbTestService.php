<?php

namespace App\Services;

use PDO;

class ConnectionDbTestService
{
    private PDO $connectionTest;
    private string|null $host = null;
    private string|null $dbName = null;
    private string|null $user = null;
    private string|null $dbPassword = null;


    public function __construct()
    {
//        $this->connectionTest = new PDO(
//            'mysql:host=' . $this->host . '; dbname=' . $this->dbName . '; charset=utf8', $this->user,
//            $this->dbPassword
//        );
//        $this->connectionTest = new PDO(
//            'mysql:host=' . getenv('APP_DB_HOST') . '; dbname=' . getenv('APP_DB_NAME') . '; charset=utf8', getenv('APP_DB_USER'), getenv('APP_DB_PASSWORD')
////            'mysql:host=localhost; dbname=APICHADOTEST; charset=utf8', 'CHADO', 'wow'
//        );
//        $this->connectionTest->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getConnectionTest(string $host, string $dbName, string $user, string $dbPassword): PDO
    {

        return  $this->connectionTest = new PDO(
            'mysql:host=' . $host . '; dbname=' . $dbName . '; charset=utf8', $user,
            $dbPassword
        );
    }
}