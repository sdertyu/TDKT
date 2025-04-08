<?php

namespace App\Http\Controllers;

use App\Models\DeXuatModel;
use App\Models\DotTDKTModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeXuatController extends Controller
{
    public function getListDeXuatTheoDot()
    {
        $user = auth()->user();

        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
            ], 404);
        }

        $hoiDong = DeXuatModel::whereHas('hoiDong', function ($query) use ($dotActive) {
            $query->where('FK_MaDot', $dotActive->PK_MaDot); // HoiDong có FK_MaDot
        })
            ->with(['danhHieu'])
            ->where('FK_User', $user->PK_MaTaiKhoan)
            ->get()
            ->map(function ($deXuat) {
                return [
                    'PK_MaDeXuat' => $deXuat->PK_MaDeXuat,
                    'NgayTao' => formatDate($deXuat->dNgayTao),
                    'tenDanhHieu' => $deXuat->danhHieu->sTenDanhHieu,
                ];
            });


        if ($hoiDong->isEmpty()) {
            return response()->json([
                'message' => 'Không có đề xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }
        return response()->json([
            'message' => 'Lấy danh sách đề xuất thành công',
            'data' => $hoiDong
        ], 200);
    }

    public function getAllDeXuatTheoDot()
    {
        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
            ], 404);
        }

        $deXuat = DeXuatModel::whereHas('hoiDong', function ($query) use ($dotActive) {
            $query->where('FK_MaDot', $dotActive->PK_MaDot); // HoiDong có FK_MaDot
        })
            ->with([
                'danhHieu',
                'taiKhoan',
                'taiKhoan.donVi',
                'taiKhoan.caNhan',
                'taiKhoan.donVi.caNhan'
            ])
            ->get();

        if ($deXuat->isEmpty()) {
            return response()->json([
                'message' => 'Không có đề xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }

        // return response()->json([
        //     'message' => 'Lấy danh sách đề xuất tetas cong',
        //     'data' => $deXuat
        // ]);

        $grouped = [];

        foreach ($deXuat as $dx) {
            $tk = $dx->taiKhoan;
            // Log::info($tk);
            // if (!$tk) continue;

            $quyen = $tk->FK_MaQuyen;

            // Nếu là tài khoản đơn vị
            if ($quyen == 4 && $tk->donVi && count($tk->donVi) > 0) {
                $donVi = $tk->donVi[0];
                Log::info($donVi);
                $maDonVi = $donVi->PK_MaDonVi;

                if (!isset($grouped[$maDonVi])) {
                    $grouped[$maDonVi] = [
                        'ma_don_vi' => $maDonVi,
                        'ten_don_vi' => $donVi->sTenDonVi,
                        'de_xuat_don_vi' => [],
                        'ca_nhan' => []
                    ];
                } else {
                    if ($grouped[$maDonVi]['ten_don_vi'] === null) {
                        $grouped[$maDonVi]['ten_don_vi'] = $donVi->sTenDonVi;
                    }
                }


                $grouped[$maDonVi]['de_xuat_don_vi'][] = $dx;
            }

            // Nếu là tài khoản cá nhân
            if ($quyen == 5 && $tk->caNhan && count($tk->caNhan) > 0) {
                $caNhan = $tk->caNhan[0];
                $maCaNhan = $caNhan->PK_MaCaNhan;
                $maDonVi = $caNhan->FK_MaDonVi;

                if (!isset($grouped[$maDonVi])) {
                    $grouped[$maDonVi] = [
                        'ma_don_vi' => $maDonVi,
                        'ten_don_vi' => null,
                        'de_xuat_don_vi' => [],
                        'ca_nhan' => []
                    ];
                }

                if (!isset($grouped[$maDonVi]['ca_nhan'][$maCaNhan])) {
                    $grouped[$maDonVi]['ca_nhan'][$maCaNhan] = [
                        'ma_ca_nhan' => $maCaNhan,
                        'ten_ca_nhan' => $caNhan->sTenCaNhan,
                        'de_xuat' => []
                    ];
                }

                $grouped[$maDonVi]['ca_nhan'][$maCaNhan]['de_xuat'][] = $dx;
            }
        }


        // Chuyển 'ca_nhan' từ map về dạng array để JSON đẹp hơn
        $final = array_map(function ($item) {
            $item['ca_nhan'] = array_values($item['ca_nhan']);
            return $item;
        }, array_values($grouped));

        // Trả kết quả
        return response()->json(['data' => $final], 200);


        // return response()->json([
        //     'message' => 'Lấy danh sách đề xuất',
        //     'data' => $grouped
        // ], 200);
    }
}
