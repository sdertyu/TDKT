<?php

namespace App\Http\Controllers;

use App\Models\DeXuatModel;
use App\Models\DotTDKTModel;
use App\Models\DotXuatModel;
use App\Models\HoiDongDonViModel;
use App\Models\HoiDongModel;
use App\Models\HoiDongTruongModel;
use App\Models\KetQuaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeXuatController extends Controller
{

    public function themDeXuatTheoDot(Request $request)
    {
        try {
            $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
            $hanNopDeXuat = $dotActive->dHanBienBanDonVi;
            $homNay = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
            if ($hanNopDeXuat < $homNay) {
                return response()->json([
                    'success' => false,
                    'error' => ['Hết thời gian nộp biên bản']
                ], 422);
            }
            $message = [
                'dexuatcanhan.required' => 'Đề xuất cá nhân không được để trống',
                'dexuatcanhan.json' => 'Đề xuất cá nhân phải là định dạng JSON',
                'dexuatdonvi.required' => 'Đề xuất đơn vị không được để trống',
                'dexuatdonvi.json' => 'Đề xuất đơn vị phải là định dạng JSON',
                'mahoidong.required' => 'Mã hội đồng không được để trống',
                'mahoidong.exists' => 'Không tìm thấy hội đồng',
            ];
            $validator = Validator::make($request->all(), [
                'dexuatcanhan' => 'required|json',
                'dexuatdonvi' => 'required|json',
                'mahoidong' => 'required|exists:tblhoidongdonvi,PK_MaHoiDong',
            ], $message);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }
            $deXuatHoiDong = DeXuatModel::where('FK_MaHoiDong', $request->mahoidong)->delete();
            $currentUser = auth()->user();
            $caNhan = json_decode($request->dexuatcanhan, true);
            foreach ($caNhan as $key => $cn) {
                foreach ($cn as $key2 => $value) {
                    // Log::info($value);
                    $dexuat = new DeXuatModel();
                    $dexuat->FK_User = $value['taiKhoan'];
                    $dexuat->FK_MaHoiDong = $request->mahoidong;
                    $dexuat->iSoNguoiBau = $value['soPhieu'];
                    $dexuat->dNgayTao = now();
                    $dexuat->FK_MaDanhHieu = $key;
                    $dexuat->save();
                }
            }

            $donVi = json_decode($request->dexuatdonvi, true);
            foreach ($donVi as $key => $dv) {
                $dexuat = new DeXuatModel();
                $dexuat->FK_User = $currentUser->PK_MaTaiKhoan;
                $dexuat->FK_MaHoiDong = $request->mahoidong;
                $dexuat->iSoNguoiBau = $dv['soPhieu'];
                $dexuat->dNgayTao = now();
                $dexuat->FK_MaDanhHieu = $dv['id'];
                $dexuat->save();
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi thêm đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getListDeXuatTheoDot()
    {
        $user = auth()->user();

        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
            ], 404);
        }

        $hoiDong = DeXuatModel::whereHas('hoiDongDonVi', function ($query) use ($dotActive) {
            $query->where('FK_MaDot', $dotActive->PK_MaDot); // HoiDong có FK_MaDot
        })
            ->with(['danhHieu', 'ketQua'])
            ->where('FK_User', $user->PK_MaTaiKhoan)
            ->get()
            ->map(function ($deXuat) {
                return [
                    'PK_MaDeXuat' => $deXuat->PK_MaDeXuat,
                    'NgayTao' => ($deXuat->dNgayTao),
                    'tenDanhHieu' => $deXuat->danhHieu->sTenDanhHieu,
                    'trangThai' => $deXuat->ketQua->bDuyet ?? null,
                ];
            });


        if ($hoiDong->isEmpty()) {
            return response()->json([
                'message' => 'Không có danh sách đề xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }
        return response()->json([
            'message' => 'Lấy danh sách đề xuất thành công',
            'data' => $hoiDong
        ], 200);
    }

    public function getListDeXuatTheoDotDotXuat()
    {
        $user = auth()->user();

        // $dotDotXuat = DotXuatModel::where('bTrangThai', 1)->first();
        // if (!$dotDotXuat) {
        //     return response()->json([
        //         'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
        //     ], 404);
        // }

        $hoiDong = DeXuatModel::whereHas('danhHieu', function ($query) {
            $query->where('FK_MaHinhThuc', 2); // HoiDong có FK_MaDot
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
                'message' => 'Không có đề xuất đột xuất nào được tìm thấy'
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

        $deXuat = DeXuatModel::whereHas('hoiDongDonVi', function ($query) use ($dotActive) {
            $query->where('FK_MaDot', $dotActive->PK_MaDot); // HoiDong có FK_MaDot
        })
            ->with([
                'danhHieu',
                'taiKhoan',
                'taiKhoan.donVi',
                'taiKhoan.caNhan',
                'taiKhoan.caNhan.donVi',
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
                $maDonVi = $donVi->PK_MaDonVi;

                if (!isset($grouped[$maDonVi])) {
                    $grouped[$maDonVi] = [
                        'ma_don_vi' => $maDonVi,
                        'ten_don_vi' => $donVi->sTenDonVi,
                        'de_xuat_don_vi' => [],
                        'ca_nhan' => []
                    ];
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
                        'ten_don_vi' => $caNhan->donVi->sTenDonVi,
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

    public function getAllDeXuatTheoDotDotXuat()
    {
        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
            ], 404);
        }

        $deXuat = DeXuatModel::whereHas('danhHieu', function ($query) {
            $query->where('FK_MaHinhThuc', 2); // HoiDong có FK_MaDot
        })
            ->whereDoesntHave('ketQua')
            ->with([
                'danhHieu',
                'taiKhoan',
                'taiKhoan.donVi',
                'taiKhoan.caNhan',
                'taiKhoan.donVi.caNhan'
            ])
            ->where('FK_MaDot', $dotActive->PK_MaDot)
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

        $deXuat = DeXuatModel::whereHas('hoiDongDonVi', function ($query) use ($dotActive) {
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


        $hoiDong = HoiDongTruongModel::where('PK_MaHoiDong', $user->sUsername . '-' . $dotActive->PK_MaDot)
            ->whereHas('kienToan')          // thêm điều kiện khác nếu cần
            ->with(['kienToan.thanhVienHoiDong'])  // load KienToan và các thành viên
            ->first();

        if ($hoiDong) {
            $hoiDong['soThanhVien'] = $hoiDong->kienToan->thanhVienHoiDong->count();
        }





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

    public function getListDeXuatXetDuyetDotXuat()
    {
        $user = auth()->user();

        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
            ], 404);
        }

        $deXuat = DeXuatModel::whereHas('danhHieu', function ($query) {
            $query->where('FK_MaHinhThuc', 2); // HoiDong có FK_MaDot
        })
            ->whereDoesntHave('ketQua')
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

        // $hoiDong = HoiDongDonViModel::where('FK_MaDotXuat', $dotDotXuat->PK_MaDotXuat)
        //     ->where('FK_MaTaiKhoan', $user->PK_MaTaiKhoan)
        //     ->first();



        if ($deXuat->isEmpty()) {
            return response()->json([
                'message' => 'Không có đề xuất nào đột xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy danh sách đề xuất thành công',
            'data' => [
                'de_xuat_don_vi' => $deXuatDonVi,
                'de_xuat_ca_nhan' => $deXuatCaNhan,
                // 'hoi_dong' => $hoiDong,
            ]
        ], 200);
    }

    public function getListDeXuatXetDuyetDotXuatTheoMa($id)
    {
        $user = auth()->user();

        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt thi đua khen thưởng nào đang hoạt động'
            ], 404);
        }

        $deXuat = DeXuatModel::whereHas('danhHieu', function ($query) {
            $query->where('FK_MaHinhThuc', 2); // HoiDong có FK_MaDot
        })
            ->whereHas('ketQua', function ($query) use ($id) {
                $query->where('FK_MaHoiDong', $id);
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

        // $hoiDong = HoiDongDonViModel::where('FK_MaDotXuat', $dotDotXuat->PK_MaDotXuat)
        //     ->where('FK_MaTaiKhoan', $user->PK_MaTaiKhoan)
        //     ->first();



        if ($deXuat->isEmpty()) {
            return response()->json([
                'message' => 'Không có đề xuất nào đột xuất nào trong đợt thi đua khen thưởng này'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy danh sách đề xuất thành công',
            'data' => [
                'de_xuat_don_vi' => $deXuatDonVi,
                'de_xuat_ca_nhan' => $deXuatCaNhan,
                // 'hoi_dong' => $hoiDong,
            ]
        ], 200);
    }


    public function xetDuyetDeXuat(Request $request)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'deXuat' => 'required|array',
            'dexuat.*.ma_de_xuat' => 'required|exists:tbldexuat,PK_MaDeXuat',
            'dexuat.*.trang_thai' => 'required|in:0,1',
            'maHoiDong' => 'required|exists:tblhoidongtruong,PK_MaHoiDong',
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
        // Log::info($request->all());
        // $validator = Validator::make($request->all(), [
        //     'caNhan.*.danhHieu' => 'required',
        //     'caNhan.*.caNhan'   => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors()
        //     ], 422);
        // }

        // Log::info($request->all());
        // return;


        $caNhan = $request->caNhan;
        $donVi = $request->donVi;
        $user = auth()->user();
        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();

        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt nào đang hoạt động'
            ], 404);
        }

        // $hanNop = $dotXuat->dHanNopDeXuat;
        // $homNay = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        // if ($hanNop < $homNay) {
        //     return response()->json([
        //         'success' => false,
        //         'error' => ['Hết thời gian nộp đề xuất']
        //     ], 422);
        // }

        try {

            DB::beginTransaction();
            // DeXuatModel::whereHas('danhHieu', function ($query) {
            //     $query->where('FK_MaHinhThuc', 2); // HoiDong có FK_MaDot
            // })
            //     ->where('FK_NguoiTao', $user->PK_MaTaiKhoan)
            //     ->delete();


            foreach ($caNhan as $dx) {
                foreach ($dx['caNhan'] as $item) {
                    DeXuatModel::create([
                        'FK_User' => $item,
                        'FK_MaDanhHieu' => $dx['danhHieu'],
                        'dNgayTao' => getDateNow(),
                        'FK_MaDot' => $dotActive->PK_MaDot,
                        'FK_NguoiTao' => $user->PK_MaTaiKhoan,
                    ]);
                }
                Log::info($dx);
            }
            foreach ($donVi as $dx) {
                DeXuatModel::create([
                    'FK_User' => $user->PK_MaTaiKhoan,
                    'FK_MaDanhHieu' => $dx['id'],
                    'dNgayTao' => getDateNow(),
                    'FK_MaDot' => $dotActive->PK_MaDot,
                    'FK_NguoiTao' => $user->PK_MaTaiKhoan,
                ]);
            }

            DB::commit();


            return response()->json([
                'message' => 'Thêm đề xuất thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi thêm đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function layThongTinDeXuatDotXuat()
    {
        $user = auth()->user();
        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'message' => 'Không có đợt đề xuất nào đang hoạt động'
            ], 404);
        }
        $deXuat = DeXuatModel::where('FK_MaDot', $dotActive->PK_MaDot)
            ->whereHas('danhHieu', function ($query) {
                $query->where('FK_MaHinhThuc', 2); // HoiDong có FK_MaDot
            })
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
            ], 200);
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
                    'danhHieu' => [
                        'maDanhHieu' => $dx->FK_MaDanhHieu,
                        'tenDanhHieu' => $dx->danhHieu ? $dx->danhHieu->sTenDanhHieu : null,
                    ],
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

    public function capNhatDeXuatDotXuat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'maDeXuat' => 'required|exists:tbldexuat,PK_MaDeXuat',
            'maDanhHieuMoi'   => 'nullable|exists:tbldanhhieu,PK_MaDanhHieu',
            'maTaiKhoanMoi'   => 'nullable|exists:tbltaikhoan,PK_MaTaiKhoan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        Log::info($request->all());

        try {
            DB::beginTransaction();

            // Xóa tất cả đề xuất của người dùng trong đợt hiện tại
            if ($request->maTaiKhoanMoi) {
                DeXuatModel::find($request->maDeXuat)->update([
                    'FK_User' => $request->maTaiKhoanMoi,
                ]);
            } else if ($request->maDanhHieuMoi) {
                DeXuatModel::find($request->maDeXuat)->update([
                    'FK_MaDanhHieu' => $request->maDanhHieuMoi,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật đề xuất thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi cập nhật đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function xoaDeXuatDotXuat($id)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $deXuat = DeXuatModel::find($id);
            if (!$deXuat) {
                return response()->json([
                    'message' => 'Không tìm thấy đề xuất'
                ], 404);
            }

            if ($deXuat->FK_NguoiTao !== $user->PK_MaTaiKhoan) {
                return response()->json([
                    'message' => 'Bạn không có quyền xóa đề xuất này'
                ], 403);
            }
            $deXuat->delete();

            DB::commit();

            return response()->json([
                'message' => 'Xóa đề xuất thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi xóa đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }
}
