<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DanhHieuController extends Controller
{
    public function index()
    {
        // $listDanhHieu = DanhHieuModel::with([
        //     'chiTietDanhHieus' => function ($query) {
        //         $query->with(['hinhThuc:PK_MaHinhThuc,sTenHinhThuc', 'loaiDanhHieu:PK_MaLoaiDanhHieu,sTenLoaiDanhhieu']);
        //     }
        // ])->get();

        $listDanhHieu = DB::table('tbldanhhieu')
            // ->join('tblchitietdanhhieu', 'tbldanhhieu.FK_MaChiTiet', '=', 'tblchitietdanhhieu.PK_MaChiTiet')
            ->join('tblhinhthuckhenthuong', 'tbldanhhieu.FK_MaHinhThuc', '=', 'tblhinhthuckhenthuong.PK_MaHinhThuc')
            ->join('tblloaidanhhieu', 'tbldanhhieu.FK_MaLoaiDanhHieu', '=', 'tblloaidanhhieu.PK_MaLoaiDanhHieu')
            ->select(
                'tbldanhhieu.PK_MaDanhHieu',
                'tbldanhhieu.sTenDanhHieu',
                'tblhinhthuckhenthuong.sTenHinhThuc',
                'tblhinhthuckhenthuong.PK_MaHinhThuc',
                'tblloaidanhhieu.sTenLoaiDanhHieu',
                'tblloaidanhhieu.PK_MaLoaiDanhHieu'
            )
            ->get();


        // $listDanhHieu = DanhHieuModel::all();

        return response()->json([
            'data' => $listDanhHieu
        ], 200);
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

        $danhHieu = new DanhHieuModel();
        $danhHieu->sTenDanhHieu = $request->tendanhhieu;
        $danhHieu->FK_MaLoaiDanhHieu = $request->loaidanhhieu;
        $danhHieu->FK_MaHinhThuc = $request->hinhthuc;
        $danhHieu->save();

        return response()->json([
            'message' => 'Thêm thành công',
            'data' => $danhHieu
        ], 200);
    }

    public function suaDanhHieu(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tbldanhhieu,PK_MaDanhHieu',
            'tendanhhieu' => 'required',
            'loaidanhhieu' => 'required|exists:tblloaidanhhieu,PK_MaLoaiDanhHieu',
            'hinhthuc' => 'required|exists:tblhinhthuckhenthuong,PK_MaHinhThuc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $danhHieu = DanhHieuModel::find($request->id);
        $danhHieu->sTenDanhHieu = $request->tendanhhieu;
        $danhHieu->FK_MaLoaiDanhHieu = $request->loaidanhhieu;
        $danhHieu->FK_MaHinhThuc = $request->hinhthuc;
        $danhHieu->save();

        return response()->json([
            'message' => 'Sửa thành công',
            'data' => $danhHieu
        ], 200);
    }

    public function xoaDanhHieu($id) {
        
        $danhHieu = DanhHieuModel::find(request()->id);
        if (!$danhHieu) {
            return response()->json([
                'message' => 'Không tìm thấy danh hiệu'
            ], 404);
        }
        $danhHieu->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ], 200);
    }
}
