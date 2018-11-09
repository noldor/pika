<?php

declare(strict_types=1);

namespace Tests\Support\Response;

use Support\Response\ExceptionJsonResponse;
use Tests\TestCase;

class ExceptionJsonResponseTest extends TestCase
{
    public function testResponseFormat(): void
    {
        $this->expectOutputString('{"result":false,"message":"some error","data":null}');

        (new ExceptionJsonResponse(500, 'some error'))->send();
    }
}
