<?php

declare(strict_types=1);

namespace Tests\Integration;

use GuzzleHttp\Exception\ClientException;
use Support\JWT;
use Tests\BrowserTestCase;

class DeleteTest extends BrowserTestCase
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
            $this->http->request(
                'DELETE',
                'api/user',
                [
                    'query' => [
                        'access_token' => JWT::encode(['email' => 'undefined@test.ru'], \APP_SECRET_KEY)
                    ]
                ]
            );
        } catch (ClientException $exception) {
            $this->assertSame(404, $exception->getResponse()->getStatusCode());
            $this->assertSame(
                '{"result":false,"message":"Can not find user with email: undefined@test.ru","data":null}',
                $exception->getResponse()->getBody()->getContents()
            );
        }
    }

    public function testDelete(): void
    {
        $result = $this->http->request(
            'DELETE',
            'api/user',
            [
                'query' => [
                    'access_token' => $this->token
                ]
            ]
        );
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('{"result":true,"message":null,"data":null}', $result->getBody()->getContents());
    }
}
