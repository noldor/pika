<?php

declare(strict_types=1);

namespace Tests\Integration;

use Support\JWT;
use Tests\BrowserTestCase;

class AuthTest extends BrowserTestCase
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

    public function testAuthWithUnknownUser(): void
    {
        $response = $this->post('api/auth', ['email' => 'test@test.ru', 'password' => '1234567']);

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => false,
                'message' => 'Invalid email or password!',
                'data' => []
            ],
            $this->jsonDecode($content)
        );
    }

    public function testAuthWithExistedUser(): void
    {
        $response = $this->post('api/auth', ['email' => 'test@test.ru', 'password' => '123456']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();

        $this->assertJson($content);

        $this->assertArraySubset(
            ['result' => true, 'message' => null],
            $this->jsonDecode($content)
        );

        $this->assertArrayHasKey('access_token', $this->jsonDecode($content)['data']);
    }
}
