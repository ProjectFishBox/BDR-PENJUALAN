<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthControllers;
use App\Http\Controllers\DashboardControllers;
use App\Http\Controllers\User\ProfileControllers;


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



