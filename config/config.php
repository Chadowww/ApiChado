<?php

define('DB_USER', getenv('DB_USER') ? getenv('DB_USER') : $_ENV['APP_DB_USER']);
define('DB_PASSWORD', getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : $_ENV['APP_DB_PASSWORD']);
define('DB_HOST', getenv('DB_HOST') ? getenv('DB_HOST') : $_ENV['APP_DB_HOST']);
define('DB_NAME', getenv('DB_NAME') ? getenv('DB_NAME') : $_ENV['APP_DB_NAME']);

//View
define('APP_VIEW_PATH', __DIR__ . '/../src/View/');

// database dump file path for automatic import
define('DB_DUMP_PATH', __DIR__ . '/../database.sql');
