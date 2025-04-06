<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DanhHieuController;
use App\Http\Controllers\DeXuatController;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\DotTDKTController;
use App\Http\Controllers\HinhThucController;
use App\Http\Controllers\HoiDongController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\ThongBaoController;
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
        Route::put('/lock/{id}', [TaiKhoanController::class, 'khoaTaiKhoan']);
        Route::delete('/delete/{id}', [TaiKhoanController::class, 'xoaTaiKhoan']);
        Route::get('/donvi', [TaiKhoanController::class, 'layDanhSachDonVi']);
        Route::get('/caNhanTrongDonVi/{id}', [TaiKhoanController::class, 'layDanhSachCaNhanTrongDonVi']);
    });
    Route::prefix('dotthiduakhenthuong')->group(function () {
        Route::get('/list', [DotTDKTController::class, 'index']);
        Route::post('/add', [DotTDKTController::class, 'themDotTDKT']);
        Route::put('/update', [DotTDKTController::class, 'SuaDotTDKT']);
        Route::delete('/delete/{id}', [DotTDKTController::class, 'XoaDotTDKT']);
        Route::put('/update-trang-thai', [DotTDKTController::class, 'suaTrangThaiDot']);
        Route::get('/thong-tin-dot/{id}', [DotTDKTController::class, 'layThongTinDot']);
        Route::get('/dot-active', [DotTDKTController::class, 'layDotActive']);
        Route::post('/them-van-ban', [DotTDKTController::class, 'themVanBanDinhKem']);
        Route::post('/update-van-ban', [DotTDKTController::class, 'suaVanBanDinhKem']);
        Route::delete('/xoa-van-ban/{id}', [DotTDKTController::class, 'xoaVanBanDinhKem']);
        Route::get('/list-van-ban/{id}', [DotTDKTController::class, 'layDanhSachVanBanDinhKem']);
        Route::get('/list-van-ban-active', [DotTDKTController::class, 'layDanhSachVanBanDinhKemActive']);
        Route::get('/previewVbdk/{id}', [DotTDKTController::class, 'viewVbdk']);
        Route::get('/downloadVbdk/{id}', [DotTDKTController::class, 'downloadVbdk']);
        Route::get('/downloadZip/{id}', [DotTDKTController::class, 'downloadZipVanBan']);

    });
    Route::prefix('hoidong')->group(function () {
        Route::get('/list', [HoiDongController::class, 'index']);
        Route::post('/add', [HoiDongController::class, 'themHoiDong']);
        Route::get('/list-hinh-thuc', [HoiDongController::class, 'layDanhSachHinhThucHD']);
        Route::get('/list-loai-hoi-dong', [HoiDongController::class, 'layDanhSachLoaiHD']);
        Route::post('/cap-nhat-hoi-dong', [HoiDongController::class, 'capNhatHoiDong']);
    });
    Route::prefix('donvi')->group(function () {
        Route::get('/list-ca-nhan/{id}', [DonViController::class, 'layDanhSachCaNhan']);
    });
    Route::prefix('danhhieu')->group(function () {
        Route::get('/list', [DanhHieuController::class, 'index']);
        Route::post('/add',[DanhHieuController::class, 'themDanhHieu']);
        Route::put('/update',[DanhHieuController::class, 'suaDanhHieu']);
        Route::put('/updatestatus/{id}',[DanhHieuController::class, 'suaTrangThai']);
        Route::delete('/delete/{id}',[DanhHieuController::class, 'xoaDanhHieu']);
    });

    Route::prefix('loaidanhhieu')->group(function () {
        Route::get('/getList', [DanhHieuController::class, 'getListLoaiDanhHieu']);
    });
    Route::prefix('capdanhhieu')->group(function () {
        Route::get('/getList', [DanhHieuController::class, 'getListCapDanhHieu']);
    });
    Route::prefix('hinhthuc')->group(function () {
        Route::get('/getList', [HinhThucController::class, 'getListHinhThuc']);
    });

    Route::prefix('thongbao')->group(function () {
        Route::get('/getlisttheoquyen', [ThongBaoController::class, 'getListThongBaoTheoQuyen']);
        Route::post('/markread', [ThongBaoController::class, 'markRead']);
        Route::get('/getthongbao/{id}', [ThongBaoController::class, 'getThongBao']);
    });

    Route::prefix('dexuat')->group(function () {
        Route::get('/getlisttheodot', [DeXuatController::class, 'getListDeXuatTheoDot']);
    });
});
