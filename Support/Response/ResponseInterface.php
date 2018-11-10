<?php

declare(strict_types=1);

namespace Support\Response;

interface ResponseInterface
{
    public function getCode(): int;

    public function getData(): ?array;

    public function send(): void;
}
