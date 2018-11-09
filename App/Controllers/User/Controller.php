<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Repositories\User;
use App\Validators\User as UserValidator;
use Support\Request\RequestInterface;
use Support\Response\ResponseInterface;

abstract class Controller
{
    /**
     * @var \Support\Request\RequestInterface
     */
    protected $request;

    /**
     * @var \App\Repositories\User
     */
    protected $userRepository;

    /**
     * @var \App\Validators\User
     */
    protected $validator;

    public function __construct(RequestInterface $request, User $userRepository)
    {
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->validator = new UserValidator($this->userRepository);
    }

    abstract public function handle(): ResponseInterface;
}
