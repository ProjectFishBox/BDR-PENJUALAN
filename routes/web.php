<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthControllers;
use App\Http\Controllers\DashboardControllers;
use App\Http\Controllers\Master\AksesControllers;
use App\Http\Controllers\Master\BarangControllers;
use App\Http\Controllers\User\ProfileControllers;
use App\Http\Controllers\Master\LokasiControllers;
use App\Http\Controllers\Master\PenggunaControllers;
use App\Http\Controllers\Master\PelangganControllers;


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
    Route::delete('pelanggan/{id}/delete', 'destroy')->name('delete-pelanggan');

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
    Route::get('/download-tamplate', 'downloadTamplate')->name('barang.download-tamplate');
    Route::post('/import-barang', 'importBarang')->name('barang.import-barang');


});





