<?php

declare(strict_types=1);

namespace Tests\Integration;

use Support\JWT;
use Tests\BrowserTestCase;

class FilterTest extends BrowserTestCase
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

    public function testFilter(): void
    {
        $response = $this->get('api/list', ['gender' => 2]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $content = $response->getBody()->getContents();
        $this->assertJson($content);
        $this->assertSame(
            [
                'result' => true,
                'message' => null,
                'data' => [
                    [
                        'id' => 5,
                        'name' => '5-name',
                        'gender' => 2,
                        'age' => 3
                    ]
                ]
            ],
            $this->jsonDecode($content)
        );
    }
}
