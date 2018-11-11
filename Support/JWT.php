<?php

declare(strict_types=1);

namespace Support;

use UnexpectedValueException;

class JWT
{
    private const TYPE = 'JWT';

    private const ALGORITHM = 'sha512';

    public static function encode(array $payload, string $key = null): string
    {
        $parts = [];

        $parts[] = static::json64Encode(
            ['alg' => static::ALGORITHM, 'typ' => static::TYPE, 'kid' => \random_int(\PHP_INT_MIN, \PHP_INT_MAX)]
        );

        $parts[] = static::json64Encode($payload);

        $parts[] = static::urlsafeB64Encode(static::sign(\implode('.', $parts), $key));

        return \implode('.', $parts);
    }

    public static function decode(string $data, string $key = null): array
    {
        $parts = \explode('.', $data);

        if (\count($parts) !== 3) {
            throw new UnexpectedValueException('Wrong number of JWT segments!');
        }

        [$header64, $payload64, $signature64] = $parts;

        $header = static::json64Decode($header64);
        $payload = static::json64Decode($payload64);
        $signature = static::urlsafeB64Decode($signature64);

        if ($header === null) {
            throw new UnexpectedValueException('Invalid token header!');
        }

        if ($payload === null) {
            throw new UnexpectedValueException('Invalid token payload!');
        }

        if ($signature === false) {
            throw new UnexpectedValueException('Invalid token signature!');
        }

        if (! isset($header['alg'])) {
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

    private static function sign($input, string $key = null): string
    {
        return \hash_hmac(static::ALGORITHM, $input, $key ?? \APP_SECRET_KEY, true);
    }

    private static function verify($data, $signature, string $key = null): bool
    {
        $hash = \hash_hmac(static::ALGORITHM, $data, $key ?? \APP_SECRET_KEY, true);

        return \hash_equals($hash, $signature);
    }

    private static function json64Encode($data): string
    {
        return static::urlsafeB64Encode(\json_encode($data));
    }

    private static function json64Decode(string $data)
    {
        return \json_decode(static::urlsafeB64Decode($data), true);
    }

    private static function urlsafeB64Decode(string $data)
    {
        return base64_decode(strtr($data, '-_', '+/'), true);
    }

    private static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}
