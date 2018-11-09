<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Support\Response\JsonResponse;
use Support\Response\ResponseInterface;

class Filter extends Controller
{
    public function handle(): ResponseInterface
    {
        return new JsonResponse(
            $this->userRepository->filter(
                $this->request->get('gender'),
                $this->request->get('age_min'),
                $this->request->get('age_max')
            )
        );
    }
}
