<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Faker\Factory;
use GuzzleHttp\Client;
use Symfony\Component\Panther\PantherTestCaseTrait;

abstract class BrowserTestCase extends TestCase
{
    use PantherTestCaseTrait;

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    public static function setUpBeforeClass(): void
    {
        static::startWebServer(__DIR__ . '/../public');
    }

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => static::$baseUri]);
        $this->faker = Factory::create();
    }

    public function createUser(string $email, string $name, string $password = '123456'): string
    {
        $result = $this->http->request('post', 'api/user', ['form_params' => [
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'dob' => (new DateTime())->format(DateTime::ATOM),
            'gender' => '2'
        ]]);

        return \json_decode($result->getBody()->getContents(), true)['data']['access_token'];
    }
}
