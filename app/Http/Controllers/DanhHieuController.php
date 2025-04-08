<?php

namespace App\Http\Controllers;

use App\Models\CapDanhHieuModel;
use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use App\Models\DeXuatModel;
use App\Models\LoaiDanhHieuModel;
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
            ->join('tblhinhthuc', 'tbldanhhieu.FK_MaHinhThuc', '=', 'tblhinhthuc.PK_MaHinhThuc', 'left')
            ->join('tblloaidanhhieu', 'tbldanhhieu.FK_MaLoaiDanhHieu', '=', 'tblloaidanhhieu.PK_MaLoaiDanhHieu', 'left')
            ->join('tblcapdanhhieu', 'tbldanhhieu.FK_MaCap', '=', 'tblcapdanhhieu.PK_MaCap', 'left')
            ->select(
                'tbldanhhieu.PK_MaDanhHieu',
                'tbldanhhieu.sTenDanhHieu',
                'tbldanhhieu.bTrangThai',
                'tblhinhthuc.sTenHinhThuc',
                'tblhinhthuc.PK_MaHinhThuc',
                'tblloaidanhhieu.sTenLoaiDanhHieu',
                'tblloaidanhhieu.PK_MaLoaiDanhHieu',
                'tblcapdanhhieu.PK_MaCap',
                'tblcapdanhhieu.sTenCap'
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
            'hinhthuc' => 'required|exists:tblhinhthuc,PK_MaHinhThuc',
            'capdanhhieu' => 'required|exists:tblcapdanhhieu,PK_MaCap',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $danhHieu = new DanhHieuModel();
            $danhHieu->sTenDanhHieu = $request->tendanhhieu;
            $danhHieu->FK_MaLoaiDanhHieu = $request->loaidanhhieu;
            $danhHieu->FK_MaHinhThuc = $request->hinhthuc;
            $danhHieu->FK_MaCap = $request->capdanhhieu;
            $danhHieu->save();

            return response()->json([
                'message' => 'Thêm thành công',
                'data' => $danhHieu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi thêm danh hiệu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function suaDanhHieu(Request $request)
    {
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

    public function suaTrangThai($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trangthai' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $danhHieu = DanhHieuModel::find($id);
            if (!$danhHieu) {
                return response()->json([
                    'message' => 'Không tìm thấy danh hiệu'
                ], 404);
            }
            $danhHieu->bTrangThai = $request->trangthai;
            $danhHieu->save();

            return response()->json([
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $danhHieu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    public function xoaDanhHieu($id)
    {

        $danhHieu = DanhHieuModel::find(request()->id);
        if (!$danhHieu) {
            return response()->json([
                'message' => 'Không tìm thấy danh hiệu'
            ], 404);
        }
        $DeXuat = DeXuatModel::where('FK_MaDanhHieu', $danhHieu->PK_MaDanhHieu)->exists();

        if ($DeXuat) {
            return response()->json([
                'message' => 'Không thể xóa danh hiệu này vì đã có trong đề xuất'
            ], 400);
        }
        try {
            $danhHieu->delete();
            return response()->json([
                'message' => 'Xóa thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xóa danh hiệu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getListLoaiDanhHieu()
    {
        try {
            $listLoaiDanhHieu = LoaiDanhHieuModel::all();
            return response()->json([
                'data' => $listLoaiDanhHieu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getListCapDanhHieu()
    {
        try {
            $listCapDanhHieu = CapDanhHieuModel::all();
            return response()->json([
                'data' => $listCapDanhHieu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
