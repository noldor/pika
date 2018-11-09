<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Support\Response\EmptyJsonResponse;
use Support\Response\ResponseInterface;

class Delete extends GuardedController
{
    public function handle(): ResponseInterface
    {
        $this->userRepository->delete($this->user->getId());

        return new EmptyJsonResponse();
    }
}
