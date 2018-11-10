<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Response;

use Support\Response\EmptyJsonResponse;
use Tests\TestCase;

class EmptyJsonResponseTest extends TestCase
{
    public function testResponseFormat(): void
    {
        $data = $this->getOutput(
            function () {
                (new EmptyJsonResponse(['key' => 'value']))->send();
            }
        );

        $this->assertSame(
            [
                'result' => true,
                'message' => null,
                'data' => null
            ],
            $data
        );
    }
}
