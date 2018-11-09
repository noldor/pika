<?php

declare(strict_types=1);

namespace Support\Response;

class ExceptionJsonResponse extends JsonResponse
{
    protected const RESULT = false;

    public function __construct(int $status = 500, string $message = '', array $headers = [])
    {
        parent::__construct(null, $status, $message, $headers);
    }
}
