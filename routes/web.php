<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthControllers;
use App\Http\Controllers\DashboardControllers;
use App\Http\Controllers\Transaksi\GabungkanControllers;
use App\Http\Controllers\Master\AksesControllers;
use App\Http\Controllers\Master\BarangControllers;
use App\Http\Controllers\User\ProfileControllers;
use App\Http\Controllers\Master\LokasiControllers;
use App\Http\Controllers\Master\PenggunaControllers;
use App\Http\Controllers\Master\PelangganControllers;
use App\Http\Controllers\Master\SetHargaControllers;
use App\Http\Controllers\Pengeluaran;
use App\Http\Controllers\Transaksi\PembelianControllers;
use App\Http\Controllers\Transaksi\PengeluaranControler;
use App\Http\Controllers\Transaksi\PenjualanControllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(AuthControllers::class)->group(function () {

    Route::get('/', 'index')->name('login');
    Route::get('/register','register')->name('register');
    Route::post('/register','register_action')->name('register_action');
    Route::post('/login','login_action')->name('login_action');
    Route::post('/logout','logout')->name('logout');
});

Route::controller(DashboardControllers::class)->group(function () {

    Route::get('/dashboard', 'index')->name('dashboard');
});

Route::controller(ProfileControllers::class)->group(function () {

    Route::get('/profile', 'index')->name('profile');
    Route::post('/user-update', 'update')->name('user-update');
});

Route::controller(LokasiControllers::class)->group(function () {

    Route::get('/lokasi', 'index')->name('lokasi');
    Route::get('/tambah-lokasi', 'create')->name('tambah-lokasi');
    Route::post('/tambah-lokasi','store')->name('store-lokasi');
    Route::get('/lokasi-edit/{id}', 'show')->name('lokasi-edit');
    Route::post('lokasi/{id}/update', 'update')->name('update-lokasi');
    Route::get('/delete-lokasi/{id}' , 'destroy')->name('delete-lokasi');

});

Route::controller(PenggunaControllers::class)->group(function () {

    Route::get('/pengguna', 'index')->name('pengguna');
    Route::get('/tambah-pengguna', 'create')->name('tambah-pengguna');
    Route::post('/tambah-pengguna', 'store')->name('store-pengguna');
    Route::get('/pengguna-edit/{id}', 'show')->name('pengguna-edit');
    Route::post('pengguna/{id}/update', 'update')->name('update-pengguna');
    Route::get('/delete-pengguna/{id}' , 'destroy')->name('delete-pengguna');

});

Route::controller(PelangganControllers::class)->group(function () {

    Route::get('/pelanggan', 'index')->name('pelanggan');
    Route::get('/tambah-pelanggan', 'create')->name('tambah-pelanggan');
    Route::post('/tambah-pelanggan', 'store')->name('store-pelanggan');
    Route::get('/pelanggan-edit/{id}', 'show')->name('pelanggan-edit');
    Route::post('pelanggan/{id}/update', 'update')->name('update-pelanggan');
    Route::get('/delete-pelanggan/{id}' , 'destroy')->name('delete-pelanggan');

});

Route::controller(AksesControllers::class)->group(function () {

    Route::get('/akses', 'index')->name('akses');
    Route::get('/tambah-akses', 'create')->name('tambah-akses');
    Route::post('/tambah-akses', 'store')->name('store-akses');
    Route::get('/akses-edit/{id}', 'show')->name('akses-edit');
    Route::post('akses/{id}/update', 'update')->name('update-akses');
    Route::get('/delete-akses/{id}' , 'destroy')->name('delete-akses');

});

Route::controller(BarangControllers::class)->group(function () {

    Route::get('/barang', 'index')->name('barang');
    Route::get('/tambah-barang', 'create')->name('tambah-barang');
    Route::post('/tambah-barang', 'store')->name('store-barang');
    Route::get('/barang-edit/{id}', 'show')->name('barang-edit');
    Route::post('barang/{id}/update', 'update')->name('update-barang');
    Route::get('/delete-barang/{id}' , 'destroy')->name('delete-barang');
    Route::get('/modal-import-barang' , 'modalImport')->name('barang.import');
    Route::get('/download-tamplate-barang', 'downloadTamplate')->name('barang.download-tamplate-barang');
    Route::post('/import-barang', 'importBarang')->name('barang.import-file');

});

Route::controller(SetHargaControllers::class)->group(function () {

    Route::get('/setharga', 'index')->name('setharga');
    Route::get('/tambah-setharga', 'create')->name('tambah-setharga');
    Route::post('/tambah-setharga', 'store')->name('store-setharga');
    Route::get('/setharga-edit/{id}', 'show')->name('setharga-edit');
    Route::post('setharga/{id}/update', 'update')->name('update-setharga');
    Route::get('/delete-setharga/{id}' , 'destroy')->name('delete-setharga');
    Route::get('/modal-import-setharga' , 'modalImport')->name('setharga.import');
    Route::get('/download-tamplate-setharga', 'downloadTamplate')->name('download-tamplate-setharga');
    Route::post('/import-setharga', 'importSetHarga')->name('import-setharga');

});

Route::controller(PembelianControllers::class)->group(function () {

    Route::get('/pembelian', 'index')->name('pembelian');
    Route::get('/tambah-pembelian', 'create')->name('tambah-pembelian');
    Route::post('/tambah-pembelian', 'store')->name('store-pembelian');
    Route::get('/pembelian-edit/{id}', 'show')->name('pembelian-edit');
    Route::get('/modal-detail-pembelian' , 'modalDetail')->name('pembelian.detail');
    Route::get('/modal-import-pembelian' , 'modalImport')->name('pembelian.import');
    Route::post('/validasi-detail-pembelian', 'validationDetail')->name('validasi-detail-pembelian');
    Route::post('pembelian/{id}/update', 'update')->name('update-pembelian');
    Route::get('/delete-pembelian/{id}' , 'destroy')->name('delete-pembelian');
    Route::get('/download-tamplate-detail-pembelian', 'downloadTamplate')->name('download-tamplate-detail-pembelian');


});

Route::controller(PengeluaranControler::class)->group(function () {

    Route::get('/pengeluaran', 'index')->name('pengeluaran');
    Route::get('/tambah-pengeluaran', 'create')->name('tambah-pengeluaran');
    Route::post('/tambah-pengeluaran', 'store')->name('store-pengeluaran');
    Route::get('/pengeluaran-edit/{id}', 'edit')->name('pengeluaran-edit');
    Route::post('pengeluaran/{id}/update', 'update')->name('update-pengeluaran');
    Route::get('/modal-detail-pengeluaran', 'show')->name('pengeluaran.detai');
    Route::get('/delete-pengeluaran/{id}' , 'destroy')->name('delete-pengeluaran');
    Route::get('/pengeluaran/export', 'export')->name('pengeluaran.export');

});


Route::controller(PenjualanControllers::class)->group(function () {

    Route::get('/penjualan', 'index')->name('penjualan');
    Route::get('/tambah-penjualan', 'create')->name('tambah-penjualan');
    Route::get('/modal-tambah-pelanggan', 'modalTambahPelanggan')->name('modal-tambah-pelanggan');
    Route::post('/tambah-pelanggan-penjualan', 'tambahPelangganPenjualan')->name('tambah-pelanggan-penjualan');
    Route::get('/pelanggan-detail/{id}', 'PelanggalDetail')->name('pelanggan-detail');
    Route::get('/modal-import-penjualan' , 'modalImport')->name('penjualan.import');
    Route::post('/validasi-detail-penjualan', 'validationDetail')->name('validasi-detail-penjualan');
    Route::post('/tambah-penjualan', 'store')->name('store-penjualan');
    Route::get('/modal-detail-penjualan' , 'modalDetail')->name('penjualan.detail');
    Route::get('/download-tamplate-detail-penjualan', 'downloadTamplate')->name('download-tamplate-detail-penjualan');
    Route::get('/penjualan-edit/{id}', 'show')->name('penjualan-edit');
    Route::post('penjualan/{id}/update', 'update')->name('update-penjualan');
    Route::get('/delete-penjualan/{id}' , 'destroy')->name('delete-penjualan');
});

Route::controller(GabungkanControllers::class)->group(function () {

    Route::get('/gabungkan', 'index')->name('gabungkan');
    Route::get('/tambah-gabungkan', 'create')->name('tambah-gabungkan');
    Route::get('/modal-import-gabungkan' , 'modalImport')->name('gabungkan.import');
    Route::get('download-tamplate-detail-gabungkan', 'downloadTamplate')->name('download-tamplate-detail-gabungkan');
    Route::post('/tambah-gabungkan', 'store')->name('store-gabungkan');
    Route::get('/modal-detail-gabungkan' , 'show')->name('gabungkan.detail');
    Route::get('/gabungkan-edit/{id}', 'edit')->name('gabungkan-edit');
    Route::post('gabungkan/{id}/update', 'update')->name('update-gabungkan');
    Route::get('/delete-gabungkan/{id}' , 'destroy')->name('delete-gabungkan');
    Route::get('/print-gabungkan/{id}' , 'printData')->name('print-gabungkan');

});




