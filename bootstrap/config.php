<?php

declare(strict_types=1);

if (! defined('PDO_DSN')) {
    define('PDO_DSN', 'sqlite:' . __DIR__ . '/../database.db');
}

if (! defined('APP_PATH')) {
    define('APP_PATH', __DIR__ . '/../');
}

if (! defined('APP_SECRET_KEY')) {
    define('APP_SECRET_KEY', 'noldor');
}

if (! defined('PDO_OPTIONS')) {
    define(
        'PDO_OPTIONS',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
}
