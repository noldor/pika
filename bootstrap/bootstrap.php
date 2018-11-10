<?php

declare(strict_types=1);

use App\Repositories\User;
use Support\Exceptions\ResponseException;
use Support\Request\Request;
use Support\Response\ExceptionJsonResponse;
use Support\Routing\Router;

require 'config.php';

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

try {
    $pdo = new PDO(PDO_DSN, '', '', PDO_OPTIONS);

    $userRepository = new User($pdo);

    $request = Request::create();

    $router = (new Router())->loadRoutes(__DIR__ . '/routes.php');

    $handler = $router->getHandler($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));

    call_user_func([new $handler($request, $userRepository), 'handle'], $request, $userRepository)->send();
} catch (PDOException $exception) {
    (new ExceptionJsonResponse(500, 'PDO exception'))->send();
} catch (ResponseException $exception) {
    (new ExceptionJsonResponse($exception->getCode(), $exception->getMessage()))->send();
} catch (Throwable $exception) {
    (new ExceptionJsonResponse(500, $exception->getMessage()))->send();
}
