<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/config.php';

function stubPath(string $path): string
{
    return __DIR__ . '/Stubs/'. $path;
}
