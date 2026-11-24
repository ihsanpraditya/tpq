<?php

declare(strict_types=1);

namespace App\Libraries;

use RuntimeException;

/**
 * Implementasi JWT (HS256) yang ringkas, tanpa dependency composer tambahan.
 *
 * Untuk kebutuhan yang lebih kompleks (rotasi kunci, algoritma lain, dsb.),
 * pertimbangkan mengganti dengan firebase/php-jwt di kemudian hari — kontrak
 * encode()/decode() di bawah ini sengaja dibuat sederhana agar mudah diganti.
 */
class Jwt
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function encode(array $payload, string $secret, int $ttlSeconds): string
    {
        $header = self::base64UrlEncode((string) json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ], JSON_THROW_ON_ERROR));

        $now = time();
        $payload['iat'] = $now;
        $payload['exp'] = $now + $ttlSeconds;

        $body = self::base64UrlEncode((string) json_encode($payload, JSON_THROW_ON_ERROR));

        $signature = self::sign("{$header}.{$body}", $secret);

        return "{$header}.{$body}.{$signature}";
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RuntimeException jika token tidak valid, kedaluwarsa, atau tanda tangan tidak cocok.
     */
    public static function decode(string $token, string $secret): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Format token tidak valid.');
        }

        [$header, $body, $signature] = $parts;

        $expectedSignature = self::sign("{$header}.{$body}", $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new RuntimeException('Tanda tangan token tidak valid.');
        }

        $payload = json_decode(self::base64UrlDecode($body), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($payload)) {
            throw new RuntimeException('Payload token tidak valid.');
        }

        if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
            throw new RuntimeException('Token sudah kedaluwarsa.');
        }

        return $payload;
    }

    private static function sign(string $data, string $secret): string
    {
        return self::base64UrlEncode(hash_hmac('sha256', $data, $secret, true));
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $padded = str_pad($data, strlen($data) % 4 === 0 ? strlen($data) : strlen($data) + (4 - strlen($data) % 4), '=');

        return (string) base64_decode(strtr($padded, '-_', '+/'), true);
    }
}
