<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[150]',
        'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[60]|is_unique[users.username,id,{id}]',
        'email'    => 'permit_empty|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role'     => 'required|in_list[admin,pengajar,wali]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan.',
        ],
        'email' => [
            'is_unique' => 'Email sudah digunakan.',
        ],
    ];

    /**
     * Cari user aktif berdasarkan username ATAU email, untuk login.
     */
    public function findForLogin(string $identifier): ?array
    {
        return $this->where('is_active', 1)
            ->groupStart()
                ->where('username', $identifier)
                ->orWhere('email', $identifier)
            ->groupEnd()
            ->first();
    }
}
