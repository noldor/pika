<?php

declare(strict_types=1);

namespace Support\Request;

class Request implements RequestInterface
{
    /**
     * @var array
     */
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public static function create(array $data = null): RequestInterface
    {
        return new static($data ?? $_REQUEST);
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->request) && $this->request[$name] !== '';
    }

    public function get(string $name, $default = null)
    {
        if (! $this->has($name)) {
            return $default;
        }

        return $this->request[$name];
    }

    public function toArray(): array
    {
        return $this->request;
    }

    public function keys(): array
    {
        return \array_keys($this->request);
    }
}
