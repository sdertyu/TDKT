<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use App\Models\ThongBaoTaiKhoanModel;
use App\Models\ThongBaoModel;
use App\Models\ThongBaoQuyenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThongBaoController extends Controller
{

    public function taoThongBao(Request $request)
    {
        $request->validate([
            'tieuDe' => 'required|string|max:255',
            'noiDung' => 'required|string',
            'quyen' => 'required',
        ]);

        try {
            $thongBao = ThongBaoModel::create([
                'sTieuDe' => $request->tieuDe,
                'sNoiDung' => $request->noiDung,
                'dNgayTao' => getDateNow(),
                'FK_MaTaiKhoan' => auth()->user()->PK_MaTaiKhoan,
            ]);

            $quyenArray = json_decode($request->quyen, true);
            $dsMaQuyen = array_column($quyenArray, 'value'); // [1, 2, ...]

            foreach ($dsMaQuyen as $maQuyen) {
                ThongBaoQuyenModel::create([
                    'FK_MaThongBao' => $thongBao->PK_MaThongBao,
                    'FK_MaQuyen' => $maQuyen,
                ]);
            }

            $taiKhoan = AccountModel::whereIn('FK_MaQuyen', $dsMaQuyen)->get();

            foreach ($taiKhoan as $tk) {
                log::info('TaiKhoan: ' . $tk);
                ThongBaoTaiKhoanModel::create([
                    'FK_MaThongBao' => $thongBao->PK_MaThongBao,
                    'FK_MaTaiKhoan' => $tk->PK_MaTaiKhoan,
                    'bTrangThai' => 0
                ]);
            }

            return response()->json([
                'message' => 'Thêm thông báo thành công'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi không xác định',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function suaThongBao($id, Request $request)
    {
        $request->validate([
            'tieuDe' => 'required|string|max:255',
            'noiDung' => 'required|string',
            'quyen' => 'required',
        ]);

        try {
            $thongBao = ThongBaoModel::find($id);
            if (!$thongBao) {
                return response()->json([
                    'message' => 'Không tìm thấy thông báo'
                ], 404);
            }

            $thongBao->update([
                'sTieuDe' => $request->tieuDe,
                'sNoiDung' => $request->noiDung,
                // 'dNgayTao' => getDateNow(),
                'FK_MaTaiKhoan' => auth()->user()->PK_MaTaiKhoan,
            ]);

            $quyenArray = json_decode($request->quyen, true);
            $dsMaQuyen = array_column($quyenArray, 'value'); // [1, 2, ...]

            // Xóa các quyền cũ
            ThongBaoQuyenModel::where('FK_MaThongBao', $id)
            ->whereNotIn('FK_MaQuyen', $dsMaQuyen)->delete();

            // Thêm quyền mới

            foreach ($dsMaQuyen as $maQuyen) {
                $thongBaoQuyen = ThongBaoQuyenModel::where('FK_MaThongBao', $id)
                    ->where('FK_MaQuyen', $maQuyen)->first();
                if ($thongBaoQuyen) {
                    // Nếu quyền đã tồn tại thì không cần thêm
                    continue;
                }
                ThongBaoQuyenModel::create([
                    'FK_MaThongBao' => $thongBao->PK_MaThongBao,
                    'FK_MaQuyen' => $maQuyen,
                ]);
            }

            // Xóa các thông báo đã đọc cũ
            ThongBaoTaiKhoanModel::whereHas('taiKhoan', function ($query) use ($dsMaQuyen) {
                $query->whereNotIn('FK_MaQuyen', $dsMaQuyen);
            })->where('FK_MaThongBao', $id)->delete();

            // Thêm thông báo mới cho các tài khoản có quyền mới
            $taiKhoan = AccountModel::whereIn('FK_MaQuyen', $dsMaQuyen)->get();
            foreach ($taiKhoan as $tk) {
                $thongBaoTaiKhoan = ThongBaoTaiKhoanModel::where('FK_MaThongBao', $id)
                    ->where('FK_MaTaiKhoan', $tk->PK_MaTaiKhoan)->first();
                if ($thongBaoTaiKhoan) {
                    // Nếu thông báo đã tồn tại thì không cần thêm
                    continue;
                }
                ThongBaoTaiKhoanModel::create([
                    'FK_MaThongBao' => $thongBao->PK_MaThongBao,
                    'FK_MaTaiKhoan' => $tk->PK_MaTaiKhoan,
                    'bTrangThai' => 0
                ]);
            }

            return response()->json([
                'message' => 'Cập nhật thông báo thành công'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi không xác định',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getListThongBaoTheoQuyen()
    {
        $maQuyen = AccountModel::where('PK_MaTaiKhoan', auth()->user()->PK_MaTaiKhoan)->first()->FK_MaQuyen;
        if (!$maQuyen) {
            return response()->json([
                'message' => 'Không tìm thấy quyền truy cập'
            ], 404);
        }
        try {
            // $thongBao = DB::table('tblthongbao')
            //     ->join('tblthongbao_quyen', 'tblthongbao.PK_MaThongBao', '=', 'tblthongbao_quyen.FK_MaThongBao')
            //     // ->join('tblquyen', 'tblthongbao_quyen.FK_MaQuyen', '=', 'tblquyen.PK_MaQuyen')
            //     ->leftJoin('tblthongbao_dadoc', function ($join) {
            //         $join->on('tblthongbao_dadoc.FK_MaThongBao', '=', 'tblthongbao.PK_MaThongBao')
            //             ->where('tblthongbao_dadoc.FK_MaTaiKhoan', auth()->user()->PK_MaTaiKhoan);
            //     })
            //     ->select(
            //         'tblthongbao.sTieuDe',
            //         'tblthongbao.sNoiDung',
            //         DB::raw('DATE_FORMAT(tblthongbao.dNgayTao, "%H:%i:%s %d/%m/%Y") as dNgayTao'),
            //         'tblthongbao_dadoc.FK_MaTaiKhoan as daDoc'
            //     )
            //     ->where('tblthongbao_quyen.FK_MaQuyen', $maQuyen)
            //     ->orderBy('tblthongbao.dNgayTao', 'desc')
            //     ->get();

            $thongBao = ThongBaoModel::with(['thongBaoTaiKhoan' => function ($query) {
                $query->where('FK_MaTaiKhoan', auth()->user()->PK_MaTaiKhoan);
            }])
                ->whereHas('thongBaoQuyen', function ($query) use ($maQuyen) {
                    if ($maQuyen !== 2) {
                        $query->where('FK_MaQuyen', $maQuyen);
                    }
                })
                ->orderByDesc('dNgayTao')
                ->get()
                ->map(function ($tb) {
                    return [
                        'PK_MaThongBao' => $tb->PK_MaThongBao,
                        'sTieuDe' => $tb->sTieuDe,
                        'sNoiDung' => $tb->sNoiDung,
                        'dNgayTao' => $tb->dNgayTao->format('H:i:s d/m/Y'),
                        'daDoc' => optional($tb->thongBaoTaiKhoan->first())->bTrangThai ?? 0,
                        'maQuyen' => $tb->thongBaoQuyen->pluck('FK_MaQuyen')->toArray(),
                    ];
                });

            // $thongBao = ThongBaoModel::with(['thongbao_quyen' => function ($query) use ($maQuyen) {
            //     $query->where('FK_MaQuyen', $maQuyen);
            // }]);

            return response()->json([
                'data' => $thongBao,
                'message' => 'Lấy danh sách thông báo thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi không xác định',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function markRead()
    {
        try {
            $user = auth()->user();

            ThongBaoTaiKhoanModel::where('FK_MaTaiKhoan', $user->PK_MaTaiKhoan)
                ->where('bTrangThai', 0)
                ->update(['bTrangThai' => 1]);

            return response()->json(['message' => 'Đánh dấu đã đọc thành công.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function getThongBao($id)
    {
        try {
            $thongBao = ThongBaoModel::whereHas('thongBaoQuyen', function ($query) {
                $query->where('FK_MaQuyen', auth()->user()->FK_MaQuyen);
            })->find($id);


            if (!$thongBao) {
                return response()->json([
                    'message' => 'Không tìm thấy thông báo'
                ], 404);
            }
            return response()->json([
                'data' => $thongBao,
                'message' => 'Lấy thông báo thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi không xác định',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
