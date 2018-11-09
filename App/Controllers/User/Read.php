<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Support\Response\JsonResponse;
use Support\Response\ResponseInterface;

class Read extends GuardedController
{
    public function handle(): ResponseInterface
    {
        return new JsonResponse($this->user->only('name', 'dob', 'gender', 'phone'));
    }
}
