<?php

declare(strict_types=1);

namespace Tests\Support\Response;

use Support\Exceptions\JsonException;
use Support\Exceptions\UnknownResponseStatusException;
use Support\Response\JsonResponse;
use Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testThatConstructThrowExceptionOnUnknownStatus(): void
    {
        $this->expectException(UnknownResponseStatusException::class);
        $this->expectExceptionMessage('Invalid response code: 600');

        new JsonResponse(null, 600);
    }

    public function testThatGetJsonContentThrowExceptionOnEncodeError(): void
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Can not encode $data to json, because Inf and NaN cannot be JSON encoded');

        (new JsonResponse([\INF]))->send();
    }

    public function testResponseFormat(): void
    {
        $this->expectOutputString('{"result":true,"message":"","data":{"key":"value"}}');

        (new JsonResponse(['key' => 'value']))->send();
    }
}
