<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongBaoController extends Controller
{
    public function getListThongBao()
    {
        $maQuyen = AccountModel::where('PK_MaTaiKhoan', auth()->user()->PK_MaTaiKhoan)->first()->FK_MaQuyen;
        if (!$maQuyen) {
            return response()->json([
                'message' => 'Không tìm thấy quyền truy cập'
            ], 404);
        }
        try {
            $thongbao = DB::table('tblthongbao')
                ->join('tblthongbao_quyen', 'tblthongbao.PK_MaThongBao', '=', 'tblthongbao_quyen.FK_MaThongBao')
                ->join('tblquyen', 'tblthongbao_quyen.FK_MaQuyen', '=', 'tblquyen.PK_MaQuyen')
                ->leftJoin('tblthongbao_dadoc', function ($join) {
                    $join->on('tblthongbao_dadoc.FK_MaThongBao', '=', 'tblthongbao.PK_MaThongBao')
                        ->where('tblthongbao_dadoc.FK_MaTaiKhoan', auth()->user()->PK_MaTaiKhoan);
                })
                ->select(
                    'tblthongbao.sTieuDe',
                    'tblthongbao.sNoiDung',
                    DB::raw('DATE_FORMAT(tblthongbao.dNgayTao, "%H:%i:%s %d/%m/%Y") as dNgayTao'),
                    'tblthongbao_dadoc.FK_MaTaiKhoan as daDoc'
                )
                ->where('tblquyen.PK_MaQuyen', $maQuyen)
                ->orderBy('tblthongbao.dNgayTao', 'desc')
                ->get();

            return response()->json([
                'data' => $thongbao,
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
        $user = auth()->user();
        $thongBao = DB::table('tblthongbao')
        ->join('tblthongbao_quyen', 'tblthongbao.PK_MaThongBao', '=', 'tblthongbao_quyen.FK_MaThongBao')
        ->join('tblquyen', 'tblthongbao_quyen.FK_MaQuyen', '=', 'tblquyen.PK_MaQuyen')
        ->select(
            'tblthongbao.sTieuDe',
            'tblthongbao.sNoiDung',
            DB::raw('DATE_FORMAT(tblthongbao.dNgayTao, "%H:%i:%s %d/%m/%Y") as dNgayTao'),
            'tblthongbao_dadoc.FK_MaTaiKhoan as daDoc'
        )
        ->where('tblquyen.PK_MaQuyen', $user->FK_MaQuyen)
        ->orderBy('tblthongbao.dNgayTao', 'desc')
        ->get();
    }
}
