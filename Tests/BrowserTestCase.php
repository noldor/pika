<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Panther\PantherTestCaseTrait;

abstract class BrowserTestCase extends DatabaseTestCase
{
    use PantherTestCaseTrait;

    /**
     * @var Client
     */
    protected $http;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::startWebServer(__DIR__ . '/server');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->http = new Client(['base_uri' => static::$baseUri]);
    }

    protected function post(string $url, array $data = []): ResponseInterface
    {
        return $this->request('POST', $url, $data);
    }

    protected function get(string $url, array $data = []): ResponseInterface
    {
        return $this->request('GET', $url, $data);
    }

    protected function put(string $url, array $data = []): ResponseInterface
    {
        return $this->request('PUT', $url, $data);
    }

    protected function delete(string $url, array $data = []): ResponseInterface
    {
        return $this->request('DELETE', $url, $data);
    }

    protected function request(string $method, string $url, array $data = []): ResponseInterface
    {
        try {
            return $this->http->request($method, $url, ['query' => $data]);
        } catch (ClientException $exception) {
            return $exception->getResponse();
        }
    }
}
