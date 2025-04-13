<?php

namespace App\Http\Controllers;

use App\Models\CapDanhHieuModel;
use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use App\Models\DeXuatModel;
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
}
