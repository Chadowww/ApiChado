<?php
use Symfony\Component\Dotenv\Dotenv;

require 'vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env.local');

try {
    $pdo = new PDO(
        'mysql:host=' . $_ENV['APP_DB_HOST'] . '; charset=utf8', $_ENV['APP_DB_USER'], $_ENV['APP_DB_PASSWORD']
    );

    $pdo->exec('DROP DATABASE IF EXISTS ' . $_ENV['APP_DB_NAME']);
    $pdo->exec('CREATE DATABASE ' . $_ENV['APP_DB_NAME']);
    $pdo->exec('USE ' . $_ENV['APP_DB_NAME']);
    if (is_file($_ENV['APP_DB_DUMP_PATH']) && is_readable($_ENV['APP_DB_DUMP_PATH'])) {
        $sql = file_get_contents($_ENV['APP_DB_DUMP_PATH']);
        dump($sql);
        $statement = $pdo->query($sql);
    } else {
        echo $_ENV['APP_DB_DUMP_PATH'] . ' file does not exist';
    }
} catch (PDOException $exception) {
    echo $exception->getMessage();
}
