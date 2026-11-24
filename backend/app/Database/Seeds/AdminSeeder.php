<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $existing = $this->db->table('users')->where('username', 'admin')->get()->getRow();

        if ($existing) {
            return;
        }

        $this->db->table('users')->insert([
            'name'       => 'Administrator',
            'username'   => 'admin',
            'email'      => 'admin@tpq.local',
            'password'   => password_hash('ubahsandiini', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
