<?php

declare(strict_types=1);

namespace Support\Exceptions;

class Unauthenticated extends ResponseException
{
    public function __construct()
    {
        parent::__construct('Missed access_token!', 401);
    }
}
