<?php

declare(strict_types=1);

namespace Support\Request;

class Request implements RequestInterface
{
    /**
     * @var array
     */
    private $post;

    public function __construct(array $post)
    {
        $this->post = $post;
    }

    public static function create(array $data = null): RequestInterface
    {
        return new static($data ?? $_REQUEST);
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->post);
    }

    public function get(string $name, $default = null)
    {
        if (! $this->has($name)) {
            return $default;
        }

        return $this->post[$name];
    }

    public function toArray(): array
    {
        return $this->post;
    }

    public function keys(): array
    {
        return \array_keys($this->post);
    }
}
