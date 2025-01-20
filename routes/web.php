<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthControllers;
use App\Http\Controllers\DashboardControllers;
use App\Http\Controllers\User\ProfileControllers;
use App\Http\Controllers\Master\LokasiControllers;
use App\Http\Controllers\Master\PenggunaControllers;


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
    Route::delete('lokasi/{id}/delete', 'destroy')->name('delete-lokasi');

});

Route::controller(PenggunaControllers::class)->group(function () {

    Route::get('/pengguna', 'index')->name('pengguna');
    Route::get('/tambah-pengguna', 'create')->name('tambah-pengguna');
    Route::post('/tambah-pengguna', 'store')->name('store-pengguna');
    Route::get('/pengguna-edit/{id}', 'show')->name('pengguna-edit');
    Route::post('pengguna/{id}/update', 'update')->name('update-pengguna');
    Route::delete('pengguna/{id}/delete', 'destroy')->name('delete-pengguna');

});



