<?php

declare(strict_types=1);

namespace Support;

use RuntimeException;
use UnexpectedValueException;

class JWT
{
    private const TYPE = 'JWT';
    private const ALGORITHM = 'sha512';

    public static function encode(array $payload, string $key): string
    {
        $parts = [];

        $parts[] = base64_encode(
            \json_encode(['alg' => static::ALGORITHM, 'typ' => static::TYPE, 'kid' => \random_int(\PHP_INT_MIN, \PHP_INT_MAX)])
        );

        $parts[] = base64_encode(\json_encode($payload));

        $parts[] = base64_encode(static::sign(\implode('.', $parts), $key));

        return \implode('.', $parts);
    }

    public static function decode(string $data, string $key): array
    {
        $parts = \explode('.', $data);

        if (\count($parts) !== 3) {
            throw new UnexpectedValueException('Wrong number of JWT segments!');
        }

        [$header64, $payload64, $signature64] = $parts;

        $header = \json_decode(\base64_decode($header64, true), true);
        $payload = \json_decode(\base64_decode($payload64, true), true);
        $signature = \base64_decode($signature64, true);

        if ($header === null) {
            throw new UnexpectedValueException('Invalid token header!');
        }

        if ($payload === null) {
            throw new UnexpectedValueException('Invalid token payload!');
        }

        if ($signature === false) {
            throw new UnexpectedValueException('Invalid token signature!');
        }

        if (!isset($header['alg'])) {
            throw new UnexpectedValueException('Empty token algorithm!');
        }

        if ($header['alg'] !== static::ALGORITHM) {
            throw new UnexpectedValueException('Unsupported token algorithm!');
        }

        if (! static::verify("{$header64}.{$payload64}", $signature, $key)) {
            throw new UnexpectedValueException('Token signature verification failed!');
        }

        return $payload;
    }

    private static function sign($input, $key): string
    {
        return \hash_hmac(static::ALGORITHM, $input, $key, true);
    }

    private static function verify($data, $signature, $key): bool
    {
        $hash = \hash_hmac(static::ALGORITHM, $data, $key, true);

        return \hash_equals($hash, $signature);
    }
}
