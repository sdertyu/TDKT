<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BaoCaoThongKeController;
use App\Http\Controllers\DanhHieuController;
use App\Http\Controllers\DeXuatController;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\DotTDKTController;
use App\Http\Controllers\HinhThucController;
use App\Http\Controllers\HoiDongController;
use App\Http\Controllers\KienToanController;
use App\Http\Controllers\MinhChungController;
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
Route::middleware(['auth.api'])->group(function () {
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
        Route::get('/getlistcanhan', [TaiKhoanController::class, 'layDanhSachToanBoCaNhan']);
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

        //ĐỢt đột xuất
        Route::get('/getdotdotxuatactive', [DotTDKTController::class, 'layThongTinDotDotXuatActive']);
        Route::get('/listdotdotxuat/{id}', [DotTDKTController::class, 'layDanhSachDotDotXuat']);
        Route::post('/adddotdotxuat', [DotTDKTController::class, 'themDotDotXuat']);
        Route::put('/updatedotdotxuat/{id}', [DotTDKTController::class, 'suaDotDotXuat']);
        Route::delete('/deletedotdotxuat/{id}', [DotTDKTController::class, 'xoaDotDotXuat']);
        Route::put('/updatetrangthaidotdotxuat/{id}', [DotTDKTController::class, 'capNhatTrangThaiDotDotXuat']);
    });
    Route::prefix('hoidong')->group(function () {
        Route::get('/list', [HoiDongController::class, 'index']);
        Route::post('/add', [HoiDongController::class, 'themHoiDong']);
        Route::get('/list-hinh-thuc', [HoiDongController::class, 'layDanhSachHinhThucHD']);
        Route::get('/list-loai-hoi-dong', [HoiDongController::class, 'layDanhSachLoaiHD']);
        Route::post('/cap-nhat-hoi-dong', [HoiDongController::class, 'capNhatHoiDong']);
        Route::get('/thongtinhoidong', [HoiDongController::class, 'layThongTinHoiDong']);
    });

    Route::prefix('donvi')->group(function () {
        Route::get('/list-ca-nhan/{id}', [DonViController::class, 'layDanhSachCaNhan']);
    });
    Route::prefix('danhhieu')->group(function () {
        Route::get('/list', [DanhHieuController::class, 'index']);
        Route::get('/listdanhhieutheodot', [DanhHieuController::class, 'layDanhSachDanhHieuTheoDot']);
        Route::post('/add', [DanhHieuController::class, 'themDanhHieu']);
        Route::put('/update', [DanhHieuController::class, 'suaDanhHieu']);
        Route::put('/updatestatus/{id}', [DanhHieuController::class, 'suaTrangThai']);
        Route::delete('/delete/{id}', [DanhHieuController::class, 'xoaDanhHieu']);
        Route::get('/listdanhhieudotxuat', [DanhHieuController::class, 'layDanhSachDanhHieuDotXuat']);
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
        Route::post('/create', [ThongBaoController::class, 'taoThongBao']);
        Route::post('/update/{id}', [ThongBaoController::class, 'suaThongBao']);
        Route::get('/getlisttheoquyen', [ThongBaoController::class, 'getListThongBaoTheoQuyen']);
        Route::post('/markread', [ThongBaoController::class, 'markRead']);
        Route::get('/getthongbao/{id}', [ThongBaoController::class, 'getThongBao']);
    });

    Route::prefix('dexuat')->group(function () {
        Route::post('/themdexuattheodot', [DeXuatController::class, 'themDeXuatTheoDot']);
        Route::get('/getlisttheodot', [DeXuatController::class, 'getListDeXuatTheoDot']);
        Route::get('/getalldexuattheodot', [DeXuatController::class, 'getAllDeXuatTheoDot']);
        Route::get('/getalldexuatdotxuat', [DeXuatController::class, 'getAllDeXuatTheoDotDotXuat']);
        Route::get('/getlistdexuatxetduyet', [DeXuatController::class, 'getListDeXuatXetDuyet']);
        Route::get('/getlistdexuatxetduyetdotxuat', [DeXuatController::class, 'getListDeXuatXetDuyetDotXuat']);
        Route::get('/getlisttheodotdotxuat', [DeXuatController::class, 'getListDeXuatTheoDotDotXuat']);
        Route::post('/xetduyetdexuat', [DeXuatController::class, 'xetDuyetDeXuat']);
        Route::post('/themdexuatdotxuat', [DeXuatController::class, 'themDeXuatDotDotXuat']);
        Route::get('/thongtindexuatdotxuat', [DeXuatController::class, 'layThongTinDeXuatDotXuat']);
    });

    Route::prefix('minhchung')->group(function () {
        Route::get('getlist/{id}', [MinhChungController::class, 'getListMinhChung']);
        Route::post('/upload', [MinhChungController::class, 'uploadMinhChung']);
        Route::get('/download/{id}', [MinhChungController::class, 'downloadMinhChung']);
        Route::post('/delete/{id}', [MinhChungController::class, 'deleteMinhChung']);
        Route::get('/preview/{id}', [MinhChungController::class, 'viewMinhChung']);
    });

    Route::prefix('baocaothongke')->group(function () {
        Route::get('thanhtichcuatoi', [BaoCaoThongKeController::class, 'thongKeThanhTichCuaToi']);
        Route::get('thanhtichcanhantrongdonvi', [BaoCaoThongKeController::class, 'thongKeThanhTichCuaCaNhanTrongDonVi']);
        Route::get('danhsachnamhoc', [BaoCaoThongKeController::class, 'danhSachNamHoc']);
        Route::get('danhsachdanhhieu', [BaoCaoThongKeController::class, 'danhSachDanhHieu']);
        Route::get('danhsachcapdanhhieu', [BaoCaoThongKeController::class, 'danhSachCapDanhHieu']);
        Route::get('danhsachdonvi', [BaoCaoThongKeController::class, 'danhSachDonVi']);
        Route::get('datathongkedanhhieu', [BaoCaoThongKeController::class, 'dataThongKeDanhHieu']);
        Route::get('datathongkecanhan', [BaoCaoThongKeController::class, 'dataThongKeCaNhan']);
        Route::get('datathongkedonvi', [BaoCaoThongKeController::class, 'dataThongKeDonVi']);
    });

    Route::prefix('kientoan')->group(function () {
        Route::get('/getlist', [KienToanController::class, 'index']);
        Route::put('/updatestatus/{id}', [KienToanController::class, 'capNhatTrangThai']);
        Route::get('/getlistnhiemvu', [KienToanController::class, 'getListNhiemVu']);
        Route::post('/create', [KienToanController::class, 'taoKienToan']);
        Route::post('/update', [KienToanController::class, 'suaKienToan']);
        Route::delete('/delete/{id}', [KienToanController::class, 'xoaKienToan']);
        Route::get('/kientoanactive', [KienToanController::class, 'layKienToanActive']);
    });

});
