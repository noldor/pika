<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Repositories\User;
use Support\Exceptions\AuthenticationFailedException;
use Support\JWT;
use Support\Request\RequestInterface;
use Support\Response\JsonResponse;
use Support\Response\ResponseInterface;

class Auth extends Controller
{
    /**
     * @var \App\Models\User
     */
    private $user;

    public function __construct(RequestInterface $request, User $userRepository)
    {
        parent::__construct($request, $userRepository);

        $this->validator->validateAuthRequest($request);

        $this->user = $userRepository->findByEmail($request->get('email'));

        if (! $this->user->verifyPassword($request->get('password'))) {
            throw new AuthenticationFailedException('Invalid email or password!');
        }
    }

    public function handle(): ResponseInterface
    {
        $accessToken = JWT::encode(['email' => $this->request->get('email')]);

        $this->userRepository->updateAccessToken($this->user->getId(), $accessToken);

        return new JsonResponse(['access_token' => $accessToken]);
    }
}
