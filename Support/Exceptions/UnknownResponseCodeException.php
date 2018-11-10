<?php

declare(strict_types=1);

namespace Support\Exceptions;

class UnknownResponseCodeException extends ResponseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}
