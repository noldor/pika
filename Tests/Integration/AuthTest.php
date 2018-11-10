<?php

declare(strict_types=1);

namespace Tests\Integration;

use GuzzleHttp\Exception\ClientException;
use Tests\BrowserTestCase;

class AuthTest extends BrowserTestCase
{
    private $userToken;

    /**
     * @var string
     */
    private $email;

    protected function setUp(): void
    {
        parent::setUp();
        $this->email = $this->faker->email;
        $this->userToken = $this->createUser($this->email, $this->faker->userName);
    }

    public function testAuthWithUnknownUser(): void
    {
        try {
            $this->http->request(
                'POST', 'api/auth', [
                    'form_params' => [
                        'email' => $this->email,
                        'password' => '1234567'
                    ]
                ]
            );
        } catch (ClientException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(
                '{"result":false,"message":"Invalid email or password!","data":null}',
                $exception->getResponse()->getBody()->getContents()
            );
        }
    }

    public function testAuthWithExistedUser(): void
    {
        $result = $this->http->request('POST', 'api/auth', ['form_params' => [
            'email' => $this->email, 'password' => '123456'
        ]]);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertJson($result->getBody()->getContents());
    }
}
