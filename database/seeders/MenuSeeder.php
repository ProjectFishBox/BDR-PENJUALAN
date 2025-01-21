<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Menu')->insert([
            [
                'Nama' => 'Dashboard',
                'icon' => '<i class="anticon anticon-step-backward"></i>',
                'url' => '/akses',
                'parent' => 0
            ],
            [
                'Nama' => 'Master Akses',
                'icon' => '<i class="anticon anticon-step-backward"></i>',
                'url' => '/akses',
                'parent' => 1
            ],
            [
                'Nama' => 'Master Pengguna',
                'icon' => '<i class="anticon anticon-step-forward"></i>',
                'url' => '/pengguna',
                'parent' => 1
            ],
            [
                'Nama' => 'Master Pelanggan',
                'icon' => '<i class="anticon anticon-fast-backward"></i>',
                'url' => '/pelanggan',
                'parent' => 1
            ],
            [
                'Nama' => 'Master Barang',
                'icon' => '<i class="anticon anticon-shrink"></i>',
                'url' => '/barang',
                'parent' => 1
            ],
            [
                'Nama' => 'Master SetHarga',
                'icon' => '<i class="anticon anticon-left"></i>',
                'url' => '/setharga',
                'parent' => 1
            ],
            [
                'Nama' => 'Pembelian',
                'icon' => '<i class="anticon anticon-up-circle"></i>',
                'url' => '/pembelian',
                'parent' => NULL
            ],
            [
                'Nama' => 'Pengeluaran',
                'icon' => '<i class="anticon anticon-vertical-right"></i>',
                'url' => '/pengeluaran',
                'parent' => NULL
            ],
            [
                'Nama' => 'Pengeluaran',
                'icon' => '<i class="anticon anticon-vertical-right"></i>',
                'url' => '/lap-pengeluaran',
                'parent' => NULL
            ],
            [
                'Nama' => 'Gabungkan',
                'icon' => '<i class="anticon anticon-retweet"></i>',
                'url' => '/gabungkan',
                'parent' => NULL
            ],
            [
                'Nama' => 'Laporan Stok',
                'icon' => '<i class="anticon anticon-right-square"></i>',
                'url' => '/stok',
                'parent' => 2
            ],
            [
                'Nama' => 'Laporan Pembelian',
                'icon' => '<i class="anticon anticon-highlight"></i>',
                'url' => '/lap-pembelian',
                'parent' => 2
            ],
            [
                'Nama' => 'Laporan Penjualan',
                'icon' => '<i class="anticon anticon-bar-chart"></i>',
                'url' => '/lap-penjualan',
                'parent' => 2
            ],
            [
                'Nama' => 'Laporan Pendapatan',
                'icon' => '<i class="anticon anticon-box-plot"></i>',
                'url' => '/pendapatan',
                'parent' => 2
            ],
        ]);
    }
}
