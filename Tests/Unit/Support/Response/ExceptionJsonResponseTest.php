<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Response;

use Support\Response\ExceptionJsonResponse;
use Tests\TestCase;

class ExceptionJsonResponseTest extends TestCase
{
    public function testResponseFormat(): void
    {
        $data = $this->getOutput(
            function () {
                (new ExceptionJsonResponse(500, 'some error'))->send();
            }
        );

        $this->assertSame(
            [
                'result' => false,
                'message' => 'some error',
                'data' => []
            ],
            $data
        );
    }
}
