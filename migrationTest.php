<?php

require 'vendor/autoload.php';
if (file_exists('config/dbTest.php')) {
    require 'config/dbTest.php';
} else {
    require 'config/dbTest.php.dist';
}

require 'config/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . '; charset=utf8', DB_USER, DB_PASSWORD
    );

    $pdo->exec('DROP DATABASE IF EXISTS ' . DB_NAME_TEST);
    $pdo->exec('CREATE DATABASE ' . DB_NAME_TEST);
    $pdo->exec('USE ' . DB_NAME_TEST);

    if (is_file(DB_DUMP_PATH_TEST) && is_readable(DB_DUMP_PATH_TEST)) {
        $sql = file_get_contents(DB_DUMP_PATH_TEST);
        $statement = $pdo->query($sql);
    } else {
        echo DB_DUMP_PATH_TEST . ' file does not exist';
    }
} catch (PDOException $exception) {
    echo $exception->getMessage();
}
