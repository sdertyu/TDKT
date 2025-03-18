<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\DotTDKTController;
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
    Route::put('/doi-mat-khau', [AccountController::class, 'changePassword']);

    Route::prefix('taikhoan')->group(function () {
        Route::get('/list', [TaiKhoanController::class, 'index']);
        Route::get('/account/{id}', [TaiKhoanController::class, 'layThongTinTaiKhoan']);
        Route::post('/add', [TaiKhoanController::class, 'themTaiKhoan']);
        Route::put('/update', [TaiKhoanController::class, 'capNhatTaiKhoan']);
        Route::delete('/delete/{id}', [TaiKhoanController::class, 'xoaTaiKhoan']);
        Route::get('/donvi', [TaiKhoanController::class, 'layDanhSachDonVi']);
    });
    Route::prefix('dotthiduakhenthuong')->group(function () {
        Route::get('/list', [DotTDKTController::class, 'index']);
        Route::post('/add', [DotTDKTController::class, 'themDotTDKT']);
        Route::post('/update-trang-thai', [DotTDKTController::class, 'suaTrangThaiDot']);
    });
});
