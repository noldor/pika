<?php

declare(strict_types=1);

if (! function_exists('sendHeader')) {
    function sendHeader(string $header, bool $replace = true)
    {
        if (! headers_sent()) {
            header($header, $replace);
        }
    }
}
