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
                $this->request->has('gender') ? (int) $this->request->get('gender') : null,
                $this->request->has('age_min') ? (int) $this->request->get('age_min') : null,
                $this->request->has('age_max') ? (int) $this->request->get('age_max') : null
            )
        );
    }
}
