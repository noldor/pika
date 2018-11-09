<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Repositories\User;
use Support\Exceptions\Unauthenticated;
use Support\Request\RequestInterface;

abstract class GuardedController extends Controller
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    public function __construct(RequestInterface $request, User $userRepository)
    {
        parent::__construct($request, $userRepository);
        $this->checkAuth($request);
        $this->user = $userRepository->findByAccessToken($request->get('access_token'));
    }

    private function checkAuth(): void
    {
        if (! $this->request->has('access_token')) {
            throw new Unauthenticated();
        }
    }
}
