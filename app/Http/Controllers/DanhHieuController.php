<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DanhHieuController extends Controller
{
    public function index()
    {
        $listDanhHieu = DanhHieuModel::all();
    }

    public function themDanhHieu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tendanhhieu' => 'required',
            'loaidanhhieu' => 'required|exists:tblloaidanhhieu,PK_MaLoaiDanhHieu',
            'hinhthuc' => 'required|exists:tblhinhthuckhenthuong,PK_MaHinhThuc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $tongChiTiet = count(ChiTietDHModel::all());

        $chiTietDH = new ChiTietDHModel();
        $chiTietDH->PK_MaChiTiet = $tongChiTiet == 0 ? 1 : $tongChiTiet + 1;
        $chiTietDH->FK_MaHinhThuc = $request->hinhthuc;
        $chiTietDH->FK_MaLoaiDanhHieu = $request->loaidanhhieu;
        $chiTietDH->save();

        $danhHieu = new DanhHieuModel();
        $danhHieu->sTenDanhHieu = $request->tendanhhieu;
        $danhHieu->FK_MaChiTiet = $tongChiTiet == 0 ? 1 : $tongChiTiet + 1;
        $danhHieu->save();

        return response()->json([
            'message' => 'Thêm thành công',
        ], 200);
    }
}
