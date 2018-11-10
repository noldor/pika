<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\Auth;
use App\Repositories\User;
use Support\Exceptions\AuthenticationFailedException;
use Support\Exceptions\EntityNotFoundException;
use Support\Exceptions\ValidationException;
use Support\JWT;
use Support\Request\Request;
use Tests\DatabaseTestCase;

class AuthTest extends DatabaseTestCase
{
    public function testConstructorThrowValidationExceptionWhenEmailDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing email!');

        new Auth(Request::create(['password' => '123456']), new User(static::$pdo));
    }

    public function testConstructorThrowValidationExceptionWhenPasswordDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing password!');

        new Auth(Request::create(['email' => 'test@test.ru']), new User(static::$pdo));
    }

    public function testConstructorThrowEntityNotFoundExceptionWhenUserNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with email: test@test.ru');

        new Auth(Request::create(['email' => 'test@test.ru', 'password' => '123456']), new User(static::$pdo));
    }

    public function testConstructorThrowExceptionWhenWrongPasswordPassed(): void
    {
        $this->createUser(
            'test@test.ru',
            'name',
            JWT::encode(['email' => 'test@test.ru']),
            '123456789'
        );

        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage('Invalid email or password!');

        new Auth(Request::create(['email' => 'test@test.ru', 'password' => '123456']), new User(static::$pdo));
    }

    public function testHandleChangeUserAccessToken(): void
    {
        $token = JWT::encode(['email' => 'test@test.ru']);
        $this->createUser('test@test.ru', 'name', $token, '123456');

        $userRepository = new User(static::$pdo);

        (new Auth(Request::create(['email' => 'test@test.ru', 'password' => '123456']), $userRepository))->handle();

        $user = $userRepository->findByEmail('test@test.ru');

        $this->assertNotSame($token, $user->getAccessToken());
    }

    public function testThatHandleReturnAccessToken(): void
    {
        $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));

        $userRepository = new User(static::$pdo);

        $response = (new Auth(
            Request::create(['email' => 'test@test.ru', 'password' => '123456']), $userRepository
        ))->handle();

        $this->assertSame(
            [
                'access_token' => $userRepository->findByEmail('test@test.ru')->getAccessToken()
            ],
            $response->getData()
        );
    }
}
