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
                'id' => 1,
                'Nama' => 'Dashboard',
                'icon' => 'anticon anticon-step-backward',
                'url' => '/dashboard',
                'parent' => 0
            ],
            [
                'id' => 2,
                'Nama' => 'Akses',
                'icon' => 'anticon anticon-step-backward',
                'url' => '/akses',
                'parent' => 1
            ],
            [
                'id' => 3,
                'Nama' => 'Pengguna',
                'icon' => 'anticon anticon-step-forward',
                'url' => '/pengguna',
                'parent' => 1
            ],
            [
                'id' => 4,
                'Nama' => 'Pelanggan',
                'icon' => 'anticon anticon-fast-backward',
                'url' => '/pelanggan',
                'parent' => 1
            ],
            [
                'id' => 5,
                'Nama' => 'Barang',
                'icon' => 'anticon anticon-shrink',
                'url' => '/barang',
                'parent' => 1
            ],
            [
                'id' => 6,
                'Nama' => 'SetHarga',
                'icon' => 'anticon anticon-left',
                'url' => '/setharga',
                'parent' => 1
            ],
            [
                'id' => 7,
                'Nama' => 'Pembelian',
                'icon' => 'anticon anticon-shopping-cart',
                'url' => '/pembelian',
                'parent' => NULL
            ],
            [
                'id' => 8,
                'Nama' => 'Pengeluaran',
                'icon' => 'anticon anticon-shopping',
                'url' => '/pengeluaran',
                'parent' => NULL
            ],
            [
                'id' => 9,
                'Nama' => 'Penjualan',
                'icon' => 'anticon anticon-shop',
                'url' => '/penjualan',
                'parent' => NULL
            ],
            [
                'id' => 10,
                'Nama' => 'Gabungkan',
                'icon' => 'anticon anticon-retweet',
                'url' => '/gabungkan',
                'parent' => NULL
            ],
            [
                'id' => 11,
                'Nama' => 'Laporan Stok',
                'icon' => 'anticon anticon-right-square',
                'url' => '/stok',
                'parent' => 2
            ],
            [
                'id' => 12,
                'Nama' => 'Laporan Pembelian',
                'icon' => 'anticon anticon-highlight',
                'url' => '/laporan-pembelian',
                'parent' => 2
            ],
            [
                'id' => 13,
                'Nama' => 'Laporan Penjualan',
                'icon' => 'anticon anticon-bar-chart',
                'url' => '/laporan-penjualan',
                'parent' => 2
            ],
            [
                'id' => 14,
                'Nama' => 'Laporan Pendapatan',
                'icon' => 'anticon anticon-box-plot',
                'url' => '/pendapatan',
                'parent' => 2
            ],
            [
                'id' => 15,
                'Nama' => 'Lokasi',
                'icon' => 'anticon anticon-environment',
                'url' => '/lokasi',
                'parent' => 1
            ]
        ]);
    }
}
