<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\Update;
use DateTime;
use Support\Exceptions\ValidationException;
use Support\JWT;
use Support\Request\Request;
use Support\Response\EmptyJsonResponse;
use Tests\DatabaseTestCase;

class UpdateTest extends DatabaseTestCase
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var \App\Models\User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = JWT::encode(['email' => 'test1@test.ru']);
        $this->user = $this->createUser(
            'test1@test.ru',
            'name1',
            $this->token
        );
    }

    public function testConstructorThrowValidationExceptionWhenEmailDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Seem`s user email is not an email!');

        new Update(
            Request::create(
                [
                    'access_token' => $this->token,
                    'email' => 'test',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenNameDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User name must contain only latin or russian characters, digits and . and -');

        new Update(
            Request::create(
                [
                    'access_token' => $this->token,
                    'email' => 'test@test.ru',
                    'name' => 'some name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenPasswordDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            'User password must have length greater or equal to 6 chars and less or equal to 255 chars!'
        );

        new Update(
            Request::create(
                [
                    'access_token' => $this->token,
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '1234',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenDobDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Seem`s that date of birth has wrong format!');

        new Update(
            Request::create(
                [
                    'access_token' => $this->token,
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::RSS),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenGenderDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            'User gender must be one of [0, 1, 2]'
        );

        new Update(
            Request::create(
                [
                    'access_token' => $this->token,
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => '3'
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenPhoneDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            'Seem`s that phone not in international phone number format!'
        );

        new Update(
            Request::create(
                [
                    'access_token' => $this->token,
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1,
                    'phone' => '12345'
                ]
            ),
            $this->userRepository
        );
    }

    public function testHandleCanUpdateUserEmail(): void
    {
        (new Update(
            Request::create(['access_token' => $this->token, 'email' => 'new@test.ru']),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            \array_merge($this->user->toArray(), ['email' => 'new@test.ru']),
            $this->userRepository->findByEmail('new@test.ru')->toArray()
        );
    }

    public function testHandleCanUpdateUserName(): void
    {
        (new Update(
            Request::create(['access_token' => $this->token, 'name' => 'new']),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            \array_merge($this->user->toArray(), ['name' => 'new']),
            $this->userRepository->findByEmail('test1@test.ru')->toArray()
        );
    }

    public function testHandleCanUpdateUserPassword(): void
    {
        (new Update(
            Request::create(['access_token' => $this->token, 'password' => '654321']),
            $this->userRepository
        ))->handle();

        $this->assertNotSame(
            $this->user->getPassword(),
            $this->userRepository->findByEmail('test1@test.ru')->getPassword()
        );
    }

    public function testHandleCanUpdateUserDob(): void
    {
        (new Update(
            Request::create(['access_token' => $this->token, 'dob' => '2011-11-09T16:22:21+00:00']),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            \array_merge($this->user->toArray(), ['dob' => '2011-11-09T16:22:21+00:00']),
            $this->userRepository->findByEmail('test1@test.ru')->toArray()
        );
    }

    public function testHandleCanUpdateUserGender(): void
    {
        (new Update(
            Request::create(['access_token' => $this->token, 'gender' => 2]),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            \array_merge($this->user->toArray(), ['gender' => 2]),
            $this->userRepository->findByEmail('test1@test.ru')->toArray()
        );
    }

    public function testHandleCanUpdateUserPhone(): void
    {
        (new Update(
            Request::create(['access_token' => $this->token, 'phone' => '+79156723497']),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            \array_merge($this->user->toArray(), ['phone' => '+79156723497']),
            $this->userRepository->findByEmail('test1@test.ru')->toArray()
        );
    }

    public function testHandleCanUpdateNothing(): void
    {
        $response = (new Update(
            Request::create(['access_token' => $this->token]),
            $this->userRepository
        ))->handle();

        $this->assertInstanceOf(EmptyJsonResponse::class, $response);
    }
}
