<?php

declare(strict_types=1);

namespace Tests\Integration;

use Support\JWT;
use Tests\BrowserTestCase;

class DeleteTest extends BrowserTestCase
{
    /**
     * @var \App\Models\User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));
    }

    public function testDeleteUnExistedUser(): void
    {
        $response = $this->delete('api/user', ['access_token' => JWT::encode(['email' => 'undefined@test.ru'])]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => false,
                'message' => 'Can not find user with given token!',
                'data' => []
            ],
            $this->jsonDecode($content)
        );
    }

    public function testDelete(): void
    {
        $response = $this->delete('api/user', ['access_token' => $this->user->getAccessToken()]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => true,
                'message' => null,
                'data' => []
            ],
            $this->jsonDecode($content)
        );
    }
}
