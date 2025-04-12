<?php

namespace App\Http\Controllers;

use App\Models\DeXuatModel;
use App\Models\DotTDKTModel;
use App\Models\DotXuatModel;
use App\Models\HoiDongModel;
use App\Models\KetQuaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            ->with(['danhHieu', 'ketQua'])
            ->where('FK_User', $user->PK_MaTaiKhoan)
            ->get()
            ->map(function ($deXuat) {
                return [
                    'PK_MaDeXuat' => $deXuat->PK_MaDeXuat,
                    'NgayTao' => formatDate($deXuat->dNgayTao),
                    'tenDanhHieu' => $deXuat->danhHieu->sTenDanhHieu,
                    'trangThai' => $deXuat->ketQua->bDuyet ?? null,
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
            if ($quyen == 4 && $tk->donVi) {
                $donVi = $tk->donVi;
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
            if ($quyen == 5 && $tk->caNhan) {
                $caNhan = $tk->caNhan;
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

    public function getListDeXuatXetDuyet()
    {
        $user = auth()->user();

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
                'taiKhoan.donVi.caNhan',
                'ketQua'
            ])

            ->get();

        $deXuatDonVi = [];
        $deXuatCaNhan = [];

        if (!$deXuat->isEmpty()) {

            foreach ($deXuat as $dx) {
                $tk = $dx->taiKhoan;
                if (!$tk) continue;

                $quyen = $tk->FK_MaQuyen;

                // Đề xuất từ đơn vị (quyền 4)
                if ($quyen == 4 && $tk->donVi) {
                    $deXuatDonVi[] = [
                        'ma_de_xuat' => $dx->PK_MaDeXuat,
                        'danh_hieu' => $dx->danhHieu ? $dx->danhHieu->sTenDanhHieu : null,
                        'don_vi' => [
                            'ma_don_vi' => $tk->donVi->PK_MaDonVi,
                            'ten_don_vi' => $tk->donVi->sTenDonVi
                        ],
                        'ngay_tao' => $dx->dNgayTao,
                        'trang_thai' => $dx->ketQua->bDuyet ?? null,
                        'so_nguoi_bau' => $dx->ketQua->iSoNguoiBau ?? null,
                        // Thêm các thông tin khác nếu cần
                    ];
                }

                // Đề xuất từ cá nhân (quyền 5)
                if ($quyen == 5 && $tk->caNhan) {
                    $deXuatCaNhan[] = [
                        'ma_de_xuat' => $dx->PK_MaDeXuat,
                        'danh_hieu' => $dx->danhHieu ? $dx->danhHieu->sTenDanhHieu : null,
                        'ca_nhan' => [
                            'ma_ca_nhan' => $tk->caNhan->PK_MaCaNhan,
                            'ten_ca_nhan' => $tk->caNhan->sTenCaNhan,
                            'chuc_vu' => $tk->caNhan->sTenChucVu,
                            'don_vi' => $tk->caNhan->donVi->sTenDonVi
                        ],
                        'ngay_tao' => $dx->dNgayTao,
                        'trang_thai' => $dx->ketQua->bDuyet ?? null,
                        'so_nguoi_bau' => $dx->ketQua->iSoNguoiBau ?? null,
                        // Thêm các thông tin khác nếu cần
                    ];
                }
            }

            // ...existing code...

            // Sắp xếp mảng đề xuất đơn vị theo tên đơn vị tăng dần
            usort($deXuatDonVi, function ($a, $b) {
                return strcmp($a['don_vi']['ten_don_vi'], $b['don_vi']['ten_don_vi']);
            });

            usort($deXuatCaNhan, function ($a, $b) {
                // So sánh tên đơn vị giảm dần (B->A)
                $donViCompare = strcmp($a['ca_nhan']['don_vi'], $b['ca_nhan']['don_vi']);

                // Nếu đơn vị khác nhau, trả về kết quả
                if ($donViCompare !== 0) {
                    return $donViCompare;
                }

                // Nếu cùng đơn vị, sắp xếp theo tên cá nhân
                $nameParts_a = explode(' ', trim($a['ca_nhan']['ten_ca_nhan']));
                $nameParts_b = explode(' ', trim($b['ca_nhan']['ten_ca_nhan']));

                // Lấy phần tử cuối cùng (tên)
                $lastName_a = end($nameParts_a);
                $lastName_b = end($nameParts_b);

                // So sánh tên
                $result = strcmp($lastName_a, $lastName_b);

                // Nếu tên giống nhau, so sánh toàn bộ họ tên
                if ($result === 0) {
                    return strcmp($a['ca_nhan']['ten_ca_nhan'], $b['ca_nhan']['ten_ca_nhan']);
                }

                return $result;
            });
        }

        $hoiDong = HoiDongModel::where('PK_MaHoiDong', $user->sUsername . '-' . $dotActive->PK_MaDot)->first();



        if ($deXuat->isEmpty()) {
            return response()->json([
                'message' => 'Không có đề xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy danh sách đề xuất thành công',
            'data' => [
                'de_xuat_don_vi' => $deXuatDonVi,
                'de_xuat_ca_nhan' => $deXuatCaNhan,
                'hoi_dong' => $hoiDong,
            ]
        ], 200);
    }


    public function xetDuyetDeXuat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deXuat' => 'required|array',
            'dexuat.*.ma_de_xuat' => 'required|exists:tbldexuat,PK_MaDeXuat',
            'dexuat.*.trang_thai' => 'required|in:0,1',
            'maHoiDong' => 'required|exists:tblhoidong,PK_MaHoiDong',
            'dexuat.*.so_nguoi_bau' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }
        try {
            $deXuat = $request->deXuat;
            // $deXuat = DeXuatModel::find($request->id);
            if (!$deXuat) {
                return response()->json([
                    'message' => 'Đã có lỗi'
                ], 404);
            }

            foreach ($deXuat as $item) {
                // Giải mã chuỗi JSON thành mảng hoặc đối tượng
                $itemData = json_decode($item, true); // true sẽ trả về mảng thay vì đối tượng

                $ketQua = KetQuaModel::where('FK_MaDeXuat', $itemData['ma_de_xuat'])->first();
                if ($ketQua) {
                    $ketQua->update([
                        'bDuyet' => $itemData['trang_thai'],
                        'dNgayDuyet' => getDateNow(),
                        'iSoNguoiBau' => $itemData['so_nguoi_bau'],
                    ]);
                } else {
                    KetQuaModel::create([
                        'FK_MaDeXuat' => $itemData['ma_de_xuat'],  // Truy cập như mảng
                        'FK_MaHoiDong' => $request->maHoiDong,
                        'bDuyet' => $itemData['trang_thai'],
                        'dNgayDuyet' => getDateNow(),
                        'iSoNguoiBau' => $itemData['so_nguoi_bau'], // Lưu ý là có thể thiếu 'so_nguoi_bau' trong dữ liệu, bạn cần kiểm tra
                    ]);
                }
            }

            return response()->json([
                'message' => 'Cập nhật trạng thái thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    public function themDeXuatDotDotXuat(Request $request)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'deXuat.*.danhHieu' => 'required',
            'deXuat.*.caNhan'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        
        $deXuat = $request->deXuat;
        $user = auth()->user();
        $dotXuat = DotXuatModel::where('bTrangThai', 1)->first();

        DeXuatModel::where('FK_MaDotXuat', $dotXuat->PK_MaDotXuat)
            ->where('FK_NguoiTao', $user->PK_MaTaiKhoan)
            ->delete();


        foreach ($deXuat as $dx) {
            foreach ($dx['caNhan'] as $item) {
                $deXuatModel = DeXuatModel::create([
                    'FK_User' => $item,
                    'FK_MaDanhHieu' => $dx['danhHieu'],
                    'dNgayTao' => getDateNow(),
                    'FK_MaDotXuat' => $dotXuat->PK_MaDotXuat,
                    'FK_NguoiTao' => $user->PK_MaTaiKhoan,
                ]);
            }
            Log::info($dx);
        }


        return response()->json([
            'message' => 'Thêm đề xuất thành công',
        ], 200);
    }

    public function layThongTinDeXuatDotXuat()
    {
        $user = auth()->user();
        $dotDotXuat = DotXuatModel::where('bTrangThai', 1)->first();
        if (!$dotDotXuat) {
            return response()->json([
                'message' => 'Không có đợt đề xuất nào đang hoạt động'
            ], 404);
        }
        $deXuat = DeXuatModel::where('FK_MaDotXuat', $dotDotXuat->PK_MaDotXuat)
            ->with([
                'danhHieu',
                'taiKhoan',
                'taiKhoan.donVi',
                'taiKhoan.caNhan',
                'taiKhoan.donVi.caNhan'
            ])
            ->where('FK_NguoiTao', $user->PK_MaTaiKhoan)
            ->get();

        if ($deXuat->isEmpty()) {
            return response()->json([
                'message' => 'Không có đề xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }
        $caNhan = [];
        $donVi = [];
        foreach ($deXuat as $dx) {
            if ($dx->danhHieu->FK_MaLoaiDanhHieu === 1) {
                $caNhan[] = [
                    'maDeXuat' => $dx->PK_MaDeXuat,
                    'danhHieu' => [
                        'maDanhHieu' => $dx->FK_MaDanhHieu,
                        'tenDanhHieu' => $dx->danhHieu ? $dx->danhHieu->sTenDanhHieu : null,
                    ],
                    'caNhan' => [
                        'taiKhoan' => $dx->FK_User,
                        'maCaNhan' => $dx->taiKhoan->caNhan->PK_MaCaNhan,
                        'tenCaNhan' => $dx->taiKhoan->caNhan->sTenCaNhan,
                    ],
                ];
            } else {
                $donVi[] = [
                    'maDeXuat' => $dx->PK_MaDeXuat,
                    'danhHieu' => $dx->danhHieu ? $dx->danhHieu->sTenDanhHieu : null,
                ];
            }
        }

        return response()->json([
            'message' => 'Lấy danh sách đề xuất thành công',
            'data' => [
                'caNhan' => $caNhan,
                'donVi' => $donVi,
                'dotXuat' => $deXuat,
            ]
        ], 200);
    }
}
