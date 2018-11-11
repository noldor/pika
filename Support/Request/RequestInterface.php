<?php

declare(strict_types=1);

namespace Support\Request;

interface RequestInterface
{
    public static function create(array $data): self;

    public function has(string $name): bool;

    public function get(string $name, $default = null);

    public function toArray(): array;

    public function keys(): array;
}
