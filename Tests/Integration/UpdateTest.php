<?php

declare(strict_types=1);

namespace Tests\Integration;

use Support\JWT;
use Tests\BrowserTestCase;

class UpdateTest extends BrowserTestCase
{
    /**
     * @var \App\Models\User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));
    }

    public function testUpdateUnknownUser(): void
    {
        $response = $this->get('api/user', ['access_token' => JWT::encode(['email' => 'undefined@test.ru'])]);

        $this->assertSame(404, $response->getStatusCode());

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

    /**
     * @covers \Support\Response\JsonResponse::send
     * @covers \Support\Response\JsonResponse::sendHeaders
     * @covers \Support\Response\JsonResponse::setResponseCode
     * @covers ::sendHeader()
     */
    public function testUpdateUser(): void
    {
        $response = $this->put(
            'api/user',
            [
                'access_token' => $this->user->getAccessToken(),
                'email' => 'new-mail@test.ru',
                'name' => 'new-name'
            ]
        );

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

        $user = $this->userRepository->findByEmail('new-mail@test.ru');
        $this->assertSame(
            \array_merge($this->user->toArray(), ['email' => 'new-mail@test.ru', 'name' => 'new-name']),
            $user->toArray()
        );
    }
}
