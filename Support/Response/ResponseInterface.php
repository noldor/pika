<?php

declare(strict_types=1);

namespace Support\Response;

interface ResponseInterface
{
    public function send(): void;
}
