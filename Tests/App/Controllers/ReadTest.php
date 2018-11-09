<?php

declare(strict_types=1);

namespace Tests\App\Controllers;

use GuzzleHttp\Exception\ClientException;
use Support\JWT;
use Tests\BrowserTestCase;

class ReadTest extends BrowserTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->email = $this->faker->email;
        $this->token = $this->createUser($this->email, $this->faker->userName);
    }

    public function testDeleteUnExistedUser(): void
    {
        try {
            $this->http->request('GET', 'api/user', ['query' => [
                'access_token' => JWT::encode(['email' => 'undefined@test.ru'], \APP_SECRET_KEY)
            ]]);
        } catch (ClientException $exception) {
            $this->assertSame(404, $exception->getResponse()->getStatusCode());
            $this->assertJson(
                '{"result":false,"message":"Can not find user with email: undefined@test.ru","data":null}',
                $exception->getResponse()->getBody()->getContents()
            );
        }
    }

    public function testDelete(): void
    {
        $result = $this->http->request('GET', 'api/user', ['query' => [
            'access_token' => $this->token
        ]]);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertJson($result->getBody()->getContents());
    }
}
