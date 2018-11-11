<?php

declare(strict_types=1);

use App\Kernel;

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

spl_autoload_register(
    function ($class) {
        $file = __DIR__ . '/../' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
);

set_error_handler(
    function ($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);

(new Kernel())->handle();
