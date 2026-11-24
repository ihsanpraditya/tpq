<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * Penyimpan sementara data user yang sedang login untuk satu siklus
 * request. Diisi oleh JwtAuthFilter setelah token tervalidasi, lalu
 * dibaca oleh controller — menghindari penambahan properti dinamis
 * ke object Request (dilarang/deprecated sejak PHP 8.2).
 */
class AuthContext
{
    /** @var array<string, mixed>|null */
    private static ?array $user = null;

    /**
     * @param array<string, mixed> $user
     */
    public static function setUser(array $user): void
    {
        self::$user = $user;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function user(): ?array
    {
        return self::$user;
    }
}
