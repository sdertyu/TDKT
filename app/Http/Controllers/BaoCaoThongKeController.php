<?php

namespace App\Http\Controllers;

use App\Models\CapDanhHieuModel;
use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use App\Models\DeXuatModel;
use App\Models\DonViModel;
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
                ])
                ->where('FK_User', $id)
                ->get()
                ->map(function ($item) {
                    return [
                        'tenDanhHieu' => $item->danhHieu->sTenDanhHieu,
                        'dot' => $item->FK_MaDot,
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

    public function thongKeThanhTichCuaCaNhanTrongDonVi()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $donVi = DonViModel::where('FK_MaTaiKhoan', $id)->first();
            if (!$donVi) {
                return response()->json([
                    'message' => 'Đơn vị không tồn tại'
                ], 404);
            }
            $maDonVi = $donVi->PK_MaDonVi;
            $thanhTich = DeXuatModel::whereHas('ketQua', function ($query) {
                $query->where('bDuyet', 1);
            })
                ->whereHas('taiKhoan.caNhan', function ($query) use ($maDonVi) {
                    $query->where('FK_MaDonVi', $maDonVi);
                })
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'taiKhoan.caNhan',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'maCaNhan' => $item->taiKhoan->caNhan->PK_MaCaNhan ?? null,
                        'tenCaNhan' => $item->taiKhoan->caNhan->sTenCaNhan ?? null,
                        'tenDanhHieu' => $item->danhHieu->sTenDanhHieu,
                        'dot' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                    ];
                });

            return response()->json([
                'data' => $thanhTich
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách thành tích của cá nhân trong đơn vị: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách thành tích của cá nhân trong đơn vị'
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

    public function dataThongKeDanhHieu()
    {
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
                    'hoiDongDonVi',
                    'taiKhoan.caNhan',
                    'taiKhoan.donVi',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'ten' => $item->taiKhoan->caNhan == null ? $item->taiKhoan->donVi->sTenDonVi : $item->taiKhoan->caNhan->sTenCaNhan,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'loai' => $item->danhHieu->loaiDanhHieu->sTenLoaiDanhHieu,
                        'doiTuong' => $item->taiKhoan->caNhan == null ? 'Đơn vị' : 'Cá nhân',
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

    public function dataThongKeCaNhan()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $data = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->whereHas('taiKhoan.caNhan.donVi',)
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'hoiDongDonVi',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'hoTen' => $item->taiKhoan->caNhan->sTenCaNhan,
                        'donVi' => $item->taiKhoan->caNhan->donVi->sTenDonVi,
                        'maDanhHieu' => $item->danhHieu->PK_MaDanhHieu,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'loai' => $item->danhHieu->loaiDanhHieu->sTenLoaiDanhHieu,
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

    public function dataThongKeDonVi()
    {
        try {

            $data = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->whereHas('taiKhoan.donVi.caNhan')
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'tenDonVi' => $item->taiKhoan->donVi->sTenDonVi,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'soLuongDat' => 1,
                    ];
                });

            // $group = [];
            // foreach ($data as $item) {
            //     if (!isset($group[$item['tenDonVi']])) {
            //         $group[$item['tenDonVi']] = 0;
            //     }
            //     $group[$item['tenDonVi']]++;
            // }

            // $data = $data->transform(function ($item) use ($group) {
            //     $item['soLuongDat'] = $group[$item['tenDonVi']];
            //     return $item;
            // });


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

    public function danhSachDonVi()
    {
        try {
            $donVi = DonViModel::select('PK_MaDonVi as maDonVi', 'sTenDonVi as tenDonVi')
                ->select('PK_MaDonVi as maDonVi', 'sTenDonVi as tenDonVi')
                ->orderBy('sTenDonVi', 'asc')
                ->get();
            return response()->json([
                'data' => $donVi
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách đơn vị: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách đơn vị'
            ], 500);
        }
    }
}
