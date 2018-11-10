<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\GuardedController;
use App\Repositories\User;
use Support\Exceptions\EntityNotFoundException;
use Support\Exceptions\Unauthenticated;
use Support\JWT;
use Support\Request\Request;
use Tests\DatabaseTestCase;

class GuardedControllerTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructorThrowUnauthenticatedExceptionWhenAccessTokenDoesNotPresent(): void
    {
        $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));

        $this->expectException(Unauthenticated::class);
        $this->expectExceptionMessage('Missing access_token!');

        $this->getMockForAbstractClass(GuardedController::class, [Request::create([]), new User(static::$pdo)]);
    }

    public function testContructorThrowExceptionWhenCanNotFindUserByAccessToken(): void
    {
        $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));

        $this->expectException(EntityNotFoundException::class);

        $this->getMockForAbstractClass(
            GuardedController::class,
            [Request::create(['access_token' => JWT::encode(['email' => 'some@test.ru'])]), new User(static::$pdo)]
        );
    }
}
