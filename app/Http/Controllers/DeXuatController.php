<?php

namespace App\Http\Controllers;

use App\Models\DeXuatModel;
use App\Models\DotTDKTModel;
use Illuminate\Http\Request;

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
        ->where('FK_User', $user->PK_MaTaiKhoan)
        ->get();

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
}
