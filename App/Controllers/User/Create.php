<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Models\User as UserModel;
use App\Repositories\User;
use Support\JWT;
use Support\Request\RequestInterface;
use Support\Response\JsonResponse;
use Support\Response\ResponseInterface;

class Create extends Controller
{
    public function __construct(RequestInterface $request, User $userRepository)
    {
        parent::__construct($request, $userRepository);
        $this->validator->validateUserInput($request);
    }

    public function handle(): ResponseInterface
    {
        $accessToken = JWT::encode(['email' => $this->request->get('email')]);

        $user = UserModel::create(
            $this->request->get('email'),
            $this->request->get('name'),
            $this->request->get('password'),
            (int) $this->request->get('gender'),
            $this->request->get('dob'),
            $this->getClientIp(),
            $accessToken,
            $this->request->get('phone')
        );

        $this->userRepository->create($user);

        return new JsonResponse(['access_token' => $accessToken]);
    }

    private function getClientIp(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    }
}
