<?php

namespace App\Http\Controllers;

use App\Models\CapDanhHieuModel;
use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use App\Models\DeXuatModel;
use App\Models\DotTDKTModel;
use App\Models\KetQuaModel;
use App\Models\LoaiDanhHieuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BaoCaoThongKeController extends Controller
{
    public function thongKeThanhTichCuaToi()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $thanhTich = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'hoiDong.dot',
                    'dotXuat.dot'
                ])
                ->where('FK_User', $id)
                ->get()
                ->map(function ($item) {
                    return [
                        'tenDanhHieu' => $item->danhHieu->sTenDanhHieu,
                        'dot' => $item->hoiDong == null ? $item->dotXuat->dot->PK_MaDot : $item->hoiDong->dot->PK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                    ];
                });

            return response()->json([
                'data' => $thanhTich
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách thành tích của tôi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách thành tích của tôi'
            ], 500);
        }
    }

    public function danhSachNamHoc()
    {
        try {
            $namHoc = DotTDKTModel::select('PK_MaDot as namHoc')
                ->orderBy('PK_MaDot', 'desc')
                ->get();
            return response()->json([
                'data' => $namHoc
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách năm học: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách năm học'
            ], 500);
        }
    }

    public function danhSachDanhHieu()
    {
        try {
            $danhHieu = DanhHieuModel::select('PK_MaDanhHieu as maDanhHieu', 'sTenDanhHieu as tenDanhHieu')
                ->orderBy('sTenDanhHieu', 'asc')
                ->get();

            return response()->json([
                'data' => $danhHieu
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách danh hiệu'
            ], 500);
        }
    }

    public function danhSachCapDanhHieu()
    {
        try {
            $capDanhHieu = CapDanhHieuModel::select('PK_MaCap as maCap', 'sTenCap as tenCap')
                ->orderBy('sTenCap', 'asc')
                ->get();

            return response()->json([
                'data' => $capDanhHieu
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách cấp danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách cấp danh hiệu'
            ], 500);
        }
    }

    public function dataThongKeDanhHieu() {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $data = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'hoiDong.dot',
                    'dotXuat.dot',
                    'taiKhoan.caNhan',
                    'taiKhoan.donVi',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'ten' => $item->taiKhoan->caNhan == null ? $item->taiKhoan->donVi->sTenDonVi : $item->taiKhoan->caNhan->sTenCaNhan,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->hoiDong == null ? $item->dotXuat->dot->PK_MaDot : $item->hoiDong->dot->PK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'loai' => $item->danhHieu->loaiDanhHieu->sTenLoaiDanhHieu,
                        'doiTuong' => $item->taiKhoan->caNhan == null ? 'Đơn vị' : 'Cá nhân',
                        // 'dot' => $item->hoiDong == null ? $item->dotXuat->dot->PK_MaDot : $item->hoiDong->dot->PK_MaDot,
                        // 'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        // 'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                    ];
                });

            return response()->json([
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy dữ liệu thống kê danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê danh hiệu'
            ], 500);
        }
    }
}
