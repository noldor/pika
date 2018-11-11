<?php

declare(strict_types=1);

if (! defined('DB_PATH')) {
    define('DB_PATH', __DIR__ . '/test.db');
}

if (! defined('PDO_DSN')) {
    define('PDO_DSN', 'sqlite:' . DB_PATH);
}
