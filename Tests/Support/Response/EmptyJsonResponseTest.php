<?php

declare(strict_types=1);

namespace Tests\Support\Response;

use Support\Response\EmptyJsonResponse;
use Tests\TestCase;

class EmptyJsonResponseTest extends TestCase
{
    public function testResponseFormat(): void
    {
        $this->expectOutputString('{"result":true,"message":"","data":null}');

        (new EmptyJsonResponse(['key' => 'value']))->send();
    }
}
