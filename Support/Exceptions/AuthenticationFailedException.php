<?php

declare(strict_types=1);

namespace Support\Exceptions;

class AuthenticationFailedException extends ResponsableException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 401);
    }
}
