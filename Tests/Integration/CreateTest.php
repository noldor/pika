<?php

declare(strict_types=1);

namespace Tests\Integration;

use DateTime;
use GuzzleHttp\Exception\ClientException;
use Tests\BrowserTestCase;

class CreateTest extends BrowserTestCase
{
    public function testCreateUserWithoutAllNecessaryFields(): void
    {
        try {
            $this->http->request(
                'post',
                'api/user',
                [
                    'form_params' => [
                        'email' => 'some@mail.ru'
                    ]
                ]
            );
        } catch (ClientException $exception) {
            $this->assertSame(400, $exception->getResponse()->getStatusCode());
            $this->assertJson(
                '{"result":false,"message":"Missing fields: [name, password, dob, gender]","data":null}',
                $exception->getResponse()->getBody()->getContents()
            );
        }
    }

    public function testCreateUserWhenValidationFailed(): void
    {
        try {
            $this->http->request(
                'post',
                'api/user',
                [
                    'form_params' => [
                        'email' => 'some',
                        'name' => 'some-name',
                        'password' => '123456',
                        'dob' => (new DateTime())->format(DateTime::ATOM),
                        'gender' => '2'
                    ]
                ]
            );
        } catch (ClientException $exception) {
            $this->assertSame(400, $exception->getResponse()->getStatusCode());
            $this->assertSame(
                '{"result":false,"message":"Seem`s user email is not an email!","data":null}',
                $exception->getResponse()->getBody()->getContents()
            );
        }
    }

    public function testCreateUser(): void
    {
        $result = $this->http->request(
            'post',
            'api/user',
            [
                'form_params' => [
                    'email' => $this->faker->email,
                    'name' => $this->faker->userName,
                    'password' => '123456',
                    'dob' => (new DateTime())->format(DateTime::ATOM),
                    'gender' => '2'
                ]
            ]
        );

        $this->assertSame(200, $result->getStatusCode());
        $this->assertArraySubset(
            [
                'result' => true,
                'message' => ''
            ],
            \json_decode($result->getBody()->getContents(), true)
        );
    }
}
