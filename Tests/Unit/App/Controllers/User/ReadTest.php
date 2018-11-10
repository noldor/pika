<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\Read;
use DateTime;
use Support\JWT;
use Support\Request\Request;
use Tests\DatabaseTestCase;

class ReadTest extends DatabaseTestCase
{
    public function testHandleReturnUserInfo(): void
    {
        $token = JWT::encode(['email' => 'test@test.ru']);
        $dob = (new DateTime())->format(DateTime::ATOM);

        $this->createUser(
            'test@test.ru',
            'name',
            $token,
            '123456',
            1,
            $dob
        );

        $response = (new Read(Request::create(['access_token' => $token]), $this->userRepository))->handle();

        $this->assertSame(['name' => 'name', 'dob' => $dob, 'gender' => 1, 'phone' => null], $response->getData());
    }

    public function testHandleReturnUserInfoWithNonNullPhone(): void
    {
        $token = JWT::encode(['email' => 'test@test.ru']);
        $dob = (new DateTime())->format(DateTime::ATOM);

        $this->createUser(
            'test@test.ru',
            'name',
            $token,
            '123456',
            1,
            $dob,
            '+79106783456'
        );

        $response = (new Read(Request::create(['access_token' => $token]), $this->userRepository))->handle();

        $this->assertSame(
            [
                'name' => 'name',
                'dob' => $dob,
                'gender' => 1,
                'phone' => '+79106783456'
            ],
            $response->getData()
        );
    }
}
