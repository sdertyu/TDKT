<?php

namespace App\Http\Controllers;

use App\Models\DotTDKTModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DotTDKTController extends Controller
{
    protected $messages = [
        'ngaytao.required' => 'Bạn cần chọn ngày tạo đợt.',
        'ngaytao.date' => 'Ngày tạo phải là một ngày hợp lệ.',
        'ngaytao.after_or_equal' => 'Ngày tạo phải không được nhỏ hơn ngày hiện tại.',
    ];
    public function index()
    {
        $listDotTDKT = DotTDKTModel::all();
        return response()->json([
            'status' => 'success',
            'data' => $listDotTDKT
        ], 200);
    }
    public function themDotTDKT(Request $request)
    {
        $ngayTao = $request->ngaytao;

        $year = Carbon::parse($ngayTao)->year;
        $month = Carbon::parse($ngayTao)->month;
        $namBatDau = $month < 5 ? ($year - 1) : $year;
        $namKetThuc = $month < 5 ? $year : $year + 1;

        $maDot = 'dot_' . $namBatDau . '_' . $namKetThuc . '_cq';

        $dotTDKT = DotTDKTModel::where('PK_MaDot', '=', $maDot)->first();

        if ($dotTDKT) {
            return response()->json([
                'message' => 'Đã tồn tại đợt thi đua này'
            ]);
        } else {
            $dotTDKT = new DotTDKTModel();
            $dotTDKT->PK_MaDot = $maDot;
            $dotTDKT->dNgayTao = $ngayTao;
            $dotTDKT->bTrangThai = 1;
            $dotTDKT->iNamBatDau = $namBatDau;
            $dotTDKT->iNamKetThuc = $namKetThuc;
            $dotTDKT->save();
        }

        return response()->json([
            'message' => 'Tạo đợt thi đua thành công',
        ], 200);
    }

    public function suaTrangThaiDot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'madot' => 'required',
            'trangthai' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $updateDotTDKT = DotTDKTModel::where('PK_MaDot', '=', $request->madot)->first();

        if ($updateDotTDKT) {
            $updateDotTDKT->bTrangThai = $request->trangthai;
            $updateDotTDKT->save();
        } else {
            return response()->json([
                'message' => 'Không tìm thấy đợt thi đua'
            ], 404);
        }

        return response()->json([
            'message' => 'Đã cập nhật trạng thái',
        ], 200);
    }

    public function layThongTinDot($id)
    {
        $dotTDKT = DotTDKTModel::where('PK_MaDot', '=', $id)->with('vanbandinhkem')->first();
        if (!$dotTDKT) {
            return response()->json([
                'message' => 'Không tìm thấy đợt thi đua tương ứng'
            ], 404);
        }

        return response()->json([
            'message' => 'Cập nhật thông tin đợt thành công',
            'data' => $dotTDKT
        ]);
    }

    public function layDotActive()
    {
        $dotTDKT = DotTDKTModel::where('bTrangThai', '=', 1)->with('vanbandinhkem')->first();

        return response()->json([
            'message' => 'Thành công',
            'data' => $dotTDKT
        ]);
    }

    public function themVanBanDinhKem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'madot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
            'file' => 'required',
        ]);

        if (!$request->hasFile('files') || !$request->file('file') == null) {
            return response()->json([
                'message' => 'Không tìm thấy file tải lên'
            ], 404);
        }

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'message' => 'Thêm thành công',
            'data' => $request->input('file')
        ]);
    }
}
