<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\TaiKhoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AccountController::class, 'index']);
Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [AccountController::class, 'logOut']);
    Route::get('/info', [AccountController::class, 'info']);

    Route::prefix('taikhoan')->group(function () {
        Route::get('/list', [TaiKhoanController::class, 'index']);
        Route::post('/add', [TaiKhoanController::class, 'themTaiKhoan']);
    });
    Route::prefix('donvi')->group(function () {
        Route::get('/list', [DonViController::class, 'index']);
        Route::post('/add', [DonViController::class, 'themDonVi']);
    });
});
