<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Repositories\User;
use Support\Request\RequestInterface;
use Support\Response\EmptyJsonResponse;
use Support\Response\ResponseInterface;

class Update extends GuardedController
{
    public function __construct(RequestInterface $request, User $userRepository)
    {
        parent::__construct($request, $userRepository);
        $this->validator->validateUserInput($request);
    }

    public function handle(): ResponseInterface
    {
        $this->user->setEmail($this->request->get('email'));
        $this->user->setName($this->request->get('name'));
        $this->user->setPassword($this->request->get('password'));
        $this->user->setDob($this->request->get('dob'));
        $this->user->setGender($this->request->get('gender'));
        $this->user->setPhone($this->request->get('phone'));

        $this->userRepository->update($this->user);

        return new EmptyJsonResponse();
    }
}
