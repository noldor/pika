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
        $this->validator->validateOptionalUserInput($request);
    }

    public function handle(): ResponseInterface
    {
        if ($this->request->has('email')) {
            $this->user->setEmail($this->request->get('email'));
        }

        if ($this->request->has('name')) {
            $this->user->setName($this->request->get('name'));
        }

        if ($this->request->has('password')) {
            $this->user->setPassword($this->request->get('password'));
        }

        if ($this->request->has('dob')) {
            $this->user->setDob($this->request->get('dob'));
        }

        if ($this->request->has('gender')) {
            $this->user->setGender((int) $this->request->get('gender'));
        }

        if ($this->request->has('phone')) {
            $this->user->setPhone($this->request->get('phone'));
        }

        $this->userRepository->update($this->user);

        return new EmptyJsonResponse();
    }
}
