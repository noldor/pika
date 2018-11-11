<?php

declare(strict_types=1);

namespace Tests\Integration;

use Support\JWT;
use Tests\BrowserTestCase;

class ReadTest extends BrowserTestCase
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

    public function testReadUnknownUser(): void
    {
        $response = $this->get('api/user', ['access_token' => JWT::encode(['email' => 'undefined@test.ru'])]);

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

    public function testReadUser(): void
    {
        $response = $this->get('api/user', ['access_token' => $this->user->getAccessToken()]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => true,
                'message' => null,
                'data' => [
                    'name' => $this->user->getName(),
                    'dob' => $this->user->getDob(),
                    'gender' => $this->user->getGender(),
                    'phone' => $this->user->getPhone()
                ]
            ],
            $this->jsonDecode($content)
        );
    }
}
