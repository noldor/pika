<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Response;

use Support\Exceptions\JsonException;
use Support\Exceptions\UnknownResponseCodeException;
use Support\Response\JsonResponse;
use Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testThatConstructThrowExceptionOnUnknownStatus(): void
    {
        $this->expectException(UnknownResponseCodeException::class);
        $this->expectExceptionMessage('Invalid response code: 600');

        new JsonResponse([], 600);
    }

    public function testThatGetJsonContentThrowExceptionOnEncodeError(): void
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Can not encode $data to json, because Inf and NaN cannot be JSON encoded');

        (new JsonResponse([\INF]))->send();
    }

    public function testGetStatus(): void
    {
        $response = new JsonResponse([], 205);

        $this->assertSame(205, $response->getCode());
    }

    public function testGetData(): void
    {
        $response = new JsonResponse(['key' => 'value', 1, 2], 307);

        $this->assertSame(['key' => 'value', 1, 2], $response->getData());
    }

    public function testResponseFormat(): void
    {
        $content = $this->getOutput(
            function () {
                (new JsonResponse(['key' => 'value']))->send();
            }
        );

        $this->assertSame(
            [
                'result' => true,
                'message' => null,
                'data' => ['key' => 'value']
            ],
            $content
        );
    }
}
