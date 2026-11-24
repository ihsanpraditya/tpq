<?php

declare(strict_types=1);

namespace App\Filters;

use App\Libraries\AuthContext;
use App\Libraries\Jwt;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

/**
 * Melindungi rute API: mewajibkan header "Authorization: Bearer <token>" yang
 * valid. Jika sah, data user (tanpa password) disisipkan ke $request->user
 * agar bisa dipakai controller berikutnya.
 *
 * Daftarkan sebagai alias 'jwtAuth' di Config\Filters, lalu pakai di route
 * group yang butuh login, misalnya:
 *   $routes->group('api', ['filter' => 'jwtAuth'], function ($routes) { ... });
 *
 * Untuk membatasi berdasarkan role, tambahkan argumen filter, contoh:
 *   'filter' => 'jwtAuth:admin,pengajar'
 */
class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (! preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return service('response')->setStatusCode(401)->setJSON([
                'message' => 'Token tidak ditemukan.',
            ]);
        }

        $secret = (string) (env('auth.jwtSecret') ?: (config('Encryption')->key ?: 'change-this-secret'));

        try {
            $payload = Jwt::decode($matches[1], $secret);
        } catch (RuntimeException $e) {
            return service('response')->setStatusCode(401)->setJSON([
                'message' => 'Token tidak valid atau kedaluwarsa.',
            ]);
        }

        $users = new UserModel();
        $user  = $users->find((int) ($payload['sub'] ?? 0));

        if ($user === null || (int) $user['is_active'] !== 1) {
            return service('response')->setStatusCode(401)->setJSON([
                'message' => 'Akun tidak ditemukan atau tidak aktif.',
            ]);
        }

        if ($arguments !== null && $arguments !== [] && ! in_array($user['role'], $arguments, true)) {
            return service('response')->setStatusCode(403)->setJSON([
                'message' => 'Anda tidak punya akses untuk resource ini.',
            ]);
        }

        AuthContext::setUser($user);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan setelah response.
    }
}
