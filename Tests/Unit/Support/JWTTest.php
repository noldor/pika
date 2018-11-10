<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Support\JWT;
use Tests\TestCase;
use UnexpectedValueException;

class JWTTest extends TestCase
{
    public function testThatEncodeCanCreateJwtToken(): void
    {
        $key = 'some key';
        $token = JWT::encode(['email' => 'test@test.ru'], $key);

        $this->assertSame(['email' => 'test@test.ru'], JWT::decode($token, $key));
    }

    public function testThatEncodeCreateDifferentTokenOnSamePayload(): void
    {
        $payload = ['email' => 'test@test.ru'];
        $token1 = JWT::encode($payload);
        $token2 = JWT::encode($payload);

        $this->assertNotSame($token1, $token2);
    }

    public function testDecodeThrowExceptionWhenTokenHaveOnePart(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Wrong number of JWT segments!');

        JWT::decode('some', 'key');
    }

    public function testDecodeThrowExceptionWhenTokenHaveTwoParts(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Wrong number of JWT segments!');

        JWT::decode('some.other', 'key');
    }

    public function testDecodeThrowExceptionWhenTokenHaveFourParts(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Wrong number of JWT segments!');

        JWT::decode('first.second.third.fourth', 'key');
    }

    public function testDecodeThrowExceptionWhenHeaderNull(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid token header!');

        $header = \base64_encode(\json_encode(null));
        $token = "{$header}.second.third";

        JWT::decode($token, 'key');
    }

    public function testDecodeThrowExceptionWhenPayloadNull(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid token payload!');

        $header = \base64_encode(\json_encode(['key' => 'value']));
        $payload = \base64_encode(\json_encode(null));
        $token = "{$header}.{$payload}.third";

        JWT::decode($token, 'key');
    }

    public function testDecodeThrowExceptionWhenSignatureNull(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid token signature!');

        $header = \base64_encode(\json_encode(['key' => 'value']));
        $payload = \base64_encode(\json_encode(['key' => 'value']));
        $signature = 'Ё';
        $token = "{$header}.{$payload}.{$signature}";

        JWT::decode($token, 'key');
    }

    public function testDecodeThrowExceptionWhenHeaderAlgDoesNotPresent(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Empty token algorithm!');

        $header = \base64_encode(\json_encode(['key' => 'value']));
        $payload = \base64_encode(\json_encode(['key' => 'value']));
        $signature = \base64_encode('signature');
        $token = "{$header}.{$payload}.{$signature}";

        JWT::decode($token, 'key');
    }

    public function testDecodeThrowExceptionWhenHeaderAlgDoesNotEqualToSha512Present(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unsupported token algorithm!');

        $header = \base64_encode(\json_encode(['alg' => 'ss']));
        $payload = \base64_encode(\json_encode(['key' => 'value']));
        $signature = \base64_encode('signature');
        $token = "{$header}.{$payload}.{$signature}";

        JWT::decode($token, 'key');
    }

    public function testDecodeThrowExceptionWhenCanNotVerifyToken(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Token signature verification failed!');

        $token = JWT::encode(['key' => 1], 'key1');

        JWT::decode($token, 'key2');
    }
}
