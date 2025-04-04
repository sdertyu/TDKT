<?php

namespace App\Http\Controllers;

use App\Models\HinhThucModel;
use Illuminate\Http\Request;

class HinhThucController extends Controller
{
    public function getListHinhThuc () {
        try {
            $listHinhThuc = HinhThucModel::all();

            return response()->json([
                'data' => $listHinhThuc
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi lấy danh sách hình thức khen thưởng: ' . $e->getMessage()
            ], 500);
        }
    }
}
