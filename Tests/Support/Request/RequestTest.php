<?php

declare(strict_types=1);

namespace Tests\Support\Request;

use Support\Request\Request;
use Tests\TestCase;

class RequestTest extends TestCase
{
    public function testThatRequestCanBeCreatedFromArray(): void
    {
        $this->assertInstanceOf(Request::class, Request::create(['key' => 'value']));
    }

    public function testCanGetDataFromRequestAsArray(): void
    {
        $this->assertSame(['key' => 'value', 'key2' => 1], Request::create(['key' => 'value', 'key2' => 1])->toArray());
    }

    public function testThatRequestCanDetermineThatKeyExists(): void
    {
        $request = Request::create(['key' => 'value']);

        $this->assertTrue($request->has('key'));
        $this->assertFalse($request->has('unknown'));
    }

    public function testThatRequestCanReturnValueByKey(): void
    {
        $request = Request::create(['key' => 'value']);

        $this->assertSame('value', $request->get('key'));
    }

    public function testThatRequestReturnDefaultValueWhenKeyDoesNotExists(): void
    {
        $request = Request::create(['key' => 'value']);

        $this->assertNull($request->get('unknown'));
        $this->assertSame(12, $request->get('unknown', 12));
    }

    public function testThatRequestKeysReturnAllKeysFromInput(): void
    {
        $request = Request::create(['key' => 'value', 'key1' => 2]);

        $this->assertSame(['key', 'key1'], $request->keys());
    }
}
