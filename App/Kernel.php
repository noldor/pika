<?php

declare(strict_types=1);

namespace App;

use App\Repositories\User;
use PDO;
use PDOException;
use Support\Exceptions\ResponsableException;
use Support\Request\Request;
use Support\Response\ExceptionJsonResponse;
use Support\Response\ResponseInterface;
use Support\Routing\Router;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class Kernel
{
    public function handle(): void
    {
        try {
            $this->createResponse()->send();
        } catch (PDOException $exception) {
            (new ExceptionJsonResponse(500, 'PDO exception'))->send();
        } catch (ResponsableException $exception) {
            (new ExceptionJsonResponse($exception->getCode(), $exception->getMessage()))->send();
        } catch (Throwable $exception) {
            (new ExceptionJsonResponse(500, $exception->getMessage()))->send();
        }
    }

    private function createResponse(): ResponseInterface
    {
        $pdo = new PDO(PDO_DSN, null, null, PDO_OPTIONS);

        $userRepository = new User($pdo);

        $request = new Request($this->getRequestData());

        $router = (new Router())->loadRoutes(ROUTES_PATH);

        $handler = $router->getHandler($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));

        return \call_user_func([new $handler($request, $userRepository), 'handle'], $request, $userRepository);
    }

    private function getRequestData(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            \parse_str(file_get_contents('php://input'), $args);

            return \array_merge($_REQUEST, $args);
        }

        return $_REQUEST;
    }
}
