<?php

declare(strict_types=1);

namespace Support\Response;

class ExceptionJsonResponse extends JsonResponse
{
    protected const RESULT = false;

    public function __construct(int $code = 500, string $message = null)
    {
        parent::__construct([], $code, $message);
    }
}
