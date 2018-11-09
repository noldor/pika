<?php

declare(strict_types=1);

namespace Support\Routing;

use RuntimeException;
use Support\Exceptions\UnknownRouteException;

class Router
{
    /**
     * @var array
     */
    private $get = [];

    /**
     * @var array
     */
    private $post = [];

    /**
     * @var array
     */
    private $put = [];

    /**
     * @var array
     */
    private $delete = [];

    public function get(string $route, string $handler): void
    {
        $this->get[$this->normalizeRoute($route)] = $this->checkHandler($handler);
    }

    public function post(string $route, string $handler): void
    {
        $this->post[$this->normalizeRoute($route)] = $this->checkHandler($handler);
    }

    public function put(string $route, string $handler): void
    {
        $this->put[$this->normalizeRoute($route)] = $this->checkHandler($handler);
    }

    public function delete(string $route, string $handler): void
    {
        $this->delete[$this->normalizeRoute($route)] = $this->checkHandler($handler);
    }

    public function has(string $method, string $route): bool
    {
        $method = $this->normalizeMethod($method);
        $route = $this->normalizeRoute($route);

        if (! \property_exists($this, $method)) {
            return false;
        }

        if (! \array_key_exists($route, $this->{$method})) {
            return false;
        }

        return true;
    }

    public function getHandler(string $method, string $route): string
    {
        $method = $this->normalizeMethod($method);
        $route = $this->normalizeRoute($route);

        if (! $this->has($method, $route)) {
            throw new UnknownRouteException("Unknown route <{$route}> for method <{$method}>!");
        }

        return $this->{$method}[$route];
    }

    public function loadRoutes($path): self
    {
        require_once $path;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'get' => $this->get,
            'post' => $this->post,
            'put' => $this->put,
            'delete' => $this->delete
        ];
    }

    private function checkHandler($handler): string
    {
        if (! \class_exists($handler)) {
            throw new RuntimeException('Route handler must be a valid class name!');
        }

        return $handler;
    }

    private function normalizeMethod(string $method): string
    {
        return \strtolower($method);
    }

    private function normalizeRoute(string $route): string
    {
        return \trim($route, '/');
    }
}
