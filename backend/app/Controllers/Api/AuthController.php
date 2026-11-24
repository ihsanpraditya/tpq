<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\AuthContext;
use App\Libraries\Jwt;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AuthController extends BaseController
{
    protected UserModel $users;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->users = new UserModel();
    }

    /**
     * POST /api/auth/login
     * Body: { "identifier": "username atau email", "password": "..." }
     */
    public function login(): ResponseInterface
    {
        $json       = $this->request->getJSON(true) ?? [];
        $identifier = trim((string) ($json['identifier'] ?? $this->request->getPost('identifier') ?? ''));
        $password   = (string) ($json['password'] ?? $this->request->getPost('password') ?? '');

        if ($identifier === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Username/email dan kata sandi wajib diisi.',
            ]);
        }

        $user = $this->users->findForLogin($identifier);

        if ($user === null || ! password_verify($password, $user['password'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Username/email atau kata sandi salah.',
            ]);
        }

        $this->users->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        $token = Jwt::encode(
            ['sub' => $user['id'], 'role' => $user['role']],
            (string) env('auth.jwtSecret', config('Encryption')->key ?: 'change-this-secret'),
            (int) env('auth.jwtTtl', 60 * 60 * 8) // 8 jam
        );

        unset($user['password']);

        return $this->response->setStatusCode(200)->setJSON([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    /**
     * GET /api/auth/me
     * Butuh header: Authorization: Bearer <token>
     * (JwtAuthFilter mengisi AuthContext setelah token tervalidasi)
     */
    public function me(): ResponseInterface
    {
        $user = AuthContext::user();

        if ($user === null) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Tidak terautentikasi.']);
        }

        unset($user['password']);

        return $this->response->setJSON(['user' => $user]);
    }

    /**
     * POST /api/auth/logout
     * Karena token bersifat stateless, logout cukup dilakukan di sisi klien
     * (hapus token tersimpan). Endpoint ini disediakan agar frontend punya
     * satu titik panggilan yang konsisten, dan siap dikembangkan jika nanti
     * dibutuhkan token blacklist.
     */
    public function logout(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Berhasil keluar.']);
    }
}
