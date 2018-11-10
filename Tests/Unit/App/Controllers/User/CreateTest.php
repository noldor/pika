<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\Create;
use DateTime;
use Support\Exceptions\ValidationException;
use Support\JWT;
use Support\Request\Request;
use Tests\DatabaseTestCase;

class CreateTest extends DatabaseTestCase
{
    public function testConstructorThrowValidationExceptionWhenEmailDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing fields: [email]');

        new Create(
            Request::create(
                [
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenNameDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing fields: [name]');

        new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenPasswordDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing fields: [password]');

        new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenDobDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing fields: [dob]');

        new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'gender' => 1
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenGenderDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing fields: [gender]');

        new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM)
                ]
            ),
            $this->userRepository
        );
    }

    public function testConstructorThrowValidationExceptionWhenEmailDoesNotValid(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Seem`s user email is not an email!');

        new Create(
            Request::create(
                [
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

    public function testConstructorThrowValidationExceptionWhenUserWithGivenEmailExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User with this email already exists!');

        $this->createUser('test@test.ru', 'name1', JWT::encode(['mail' => 'test@test.ru']));

        new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
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

        new Create(
            Request::create(
                [
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

    public function testConstructorThrowValidationExceptionWhenUserWithGivenNameExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User with this name already exists!');

        $this->createUser('test1@test.ru', 'name', JWT::encode(['mail' => 'test@test.ru']));

        new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'name' => 'name',
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

        new Create(
            Request::create(
                [
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

        new Create(
            Request::create(
                [
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

        new Create(
            Request::create(
                [
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

        new Create(
            Request::create(
                [
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

    public function testHandleCanCreateUser(): void
    {
        (new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        ))->handle();

        $user = $this->userRepository->findByEmail('test@test.ru');

        $this->assertArraySubset(['email' => 'test@test.ru', 'name' => 'name', 'gender' => 1], $user->toArray());
    }

    public function testHandleReturnUserAccessToken(): void
    {
        $response = (new Create(
            Request::create(
                [
                    'email' => 'test@test.ru',
                    'name' => 'name',
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => 1
                ]
            ),
            $this->userRepository
        ))->handle();

        $user = $this->userRepository->findByEmail('test@test.ru');

        $this->assertSame(['access_token' => $user->getAccessToken()], $response->getData());
    }
}
