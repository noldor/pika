<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getOutput(Closure $callback)
    {
        \ob_start();
        $callback();
        $content = \ob_get_contents();
        \ob_end_clean();

        return $this->jsonDecode($content);
    }

    protected function jsonDecode(string $json)
    {
        return \json_decode($json, true);
    }
}
