<?php

declare(strict_types=1);

namespace Tests\Integration;

use DateTime;
use Tests\BrowserTestCase;

class CreateTest extends BrowserTestCase
{
    public function testCreateUserWithoutAllNecessaryFields(): void
    {
        $response = $this->post('api/user', ['email' => 'some@mail.ru']);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => false,
                'message' => 'Missing fields: [name, password, dob, gender]',
                'data' => []
            ],
            $this->jsonDecode($content)
        );
    }

    public function testCreateUserWhenValidationFailed(): void
    {
        $response = $this->post(
            'api/user',
            [
                'email' => 'some',
                'name' => 'some-name',
                'password' => '123456',
                'dob' => (new DateTime())->format(DateTime::ATOM),
                'gender' => '2'
            ]
        );

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => false,
                'message' => 'Seem`s user email is not an email!',
                'data' => []
            ],
            $this->jsonDecode($content)
        );
    }

    public function testCreateUser(): void
    {
        $response = $this->post(
            'api/user',
            [
                'email' => 'test@test.ru',
                'name' => 'name',
                'password' => '123456',
                'dob' => (new DateTime())->format(DateTime::ATOM),
                'gender' => '2'
            ]
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $this->assertArraySubset(
            [
                'result' => true,
                'message' => ''
            ],
            \json_decode($response->getBody()->getContents(), true)
        );
    }
}
