<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use App\Models\ThongBaoDaDocModel;
use App\Models\ThongBaoModel;
use App\Models\ThongBaoQuyenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongBaoController extends Controller
{
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

            $thongBao = ThongBaoModel::with(['thongBaoDaDoc' => function ($query) {
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
                        'daDoc' => optional($tb->thongBaoDaDoc->first())->FK_MaTaiKhoan,
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

            $maQuyen = AccountModel::where('PK_MaTaiKhoan', $user->PK_MaTaiKhoan)
                ->value('FK_MaQuyen');

            $thongBaos = ThongBaoModel::with(['thongBaoDaDoc' => function ($query) use ($user) {
                $query->where('FK_MaTaiKhoan', $user->PK_MaTaiKhoan);
            }])
                ->whereHas('thongBaoQuyen', function ($query) use ($maQuyen) {
                    if ($maQuyen !== 2) {
                        $query->where('FK_MaQuyen', $maQuyen);
                    }
                })
                ->orderByDesc('dNgayTao')
                ->get();

            foreach ($thongBaos as $tb) {
                $daDoc = $tb->thongBaoDaDoc->first();
                if (!$daDoc) {
                    ThongBaoDaDocModel::create([
                        'FK_MaThongBao' => $tb->PK_MaThongBao,
                        'FK_MaTaiKhoan' => $user->PK_MaTaiKhoan,
                    ]);
                }
            }

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
