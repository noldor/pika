<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\Delete;
use Support\JWT;
use Support\Request\Request;
use Support\Response\EmptyJsonResponse;
use Tests\DatabaseTestCase;

class DeleteTest extends DatabaseTestCase
{
    public function testHandleCanDeleteUser(): void
    {
        $user = $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));

        (new Delete(Request::create(['access_token' => $user->getAccessToken()]), $this->userRepository))->handle();

        $this->assertFalse($this->userRepository->hasByEmail('test@test.ru'));
    }

    public function testHandleReturnEmptyJsonResponse(): void
    {
        $user = $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));

        $response = (new Delete(Request::create(['access_token' => $user->getAccessToken()]), $this->userRepository))
            ->handle();

        $this->assertInstanceOf(EmptyJsonResponse::class, $response);
    }
}
