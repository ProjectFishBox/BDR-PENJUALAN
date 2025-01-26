<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nama' => 'admin',
                'username' => 'admin',
                'password' => '$2y$10$steBIIhgSEaQL85F/XnnjuOIuF4qAhxxbgkY607phxR7fuMt9VD1q',
                'jabatan' => 'admin',
                'id_lokasi' => 1,
                'id_akses' => 1,
                'create_by' => 1,
                'last_user' => 1,
            ],
            [
                'nama' => 'user',
                'username' => 'user',
                'password' => '$2y$10$steBIIhgSEaQL85F/XnnjuOIuF4qAhxxbgkY607phxR7fuMt9VD1q',
                'jabatan' => 'user',
                'id_lokasi' => 2,
                'id_akses' => 2,
                'create_by' => 1,
                'last_user' => 1,
            ]
        ]);
    }
}
