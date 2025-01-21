<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kota')->insert([
            [
                'nama' => 'Makassar',
                'create_by' => 1,
                'last_user' => 1,
            ],
            [
                'nama' => 'Jakarta',
                'create_by' => 1,
                'last_user' => 1,
            ],
            [
                'nama' => 'Bandung',
                'create_by' => 1,
                'last_user' => 1,
            ],
        ]);
    }
}
