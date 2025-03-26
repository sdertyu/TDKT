<?php

namespace App\Http\Controllers;

use App\Models\ChiTietHDModel;
use App\Models\HinhThucHDModel;
use App\Models\HoiDongModel;
use App\Models\LoaiHDModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class HoiDongController extends Controller
{
    protected $messages = [
        'madot.required' => 'Vui lòng chọn đợt thi đua.',
        'madot.exists' => 'Đợt thi đua đã chọn không tồn tại.',

        'machutri.required' => 'Vui lòng chọn tài khoản chủ trì.',
        'machutri.exists' => 'Tài khoản chủ trì không tồn tại.',

        'mathuky.required' => 'Vui lòng chọn tài khoản thư ký.',
        'mathuky.exists' => 'Tài khoản thư ký không tồn tại.',

        'thoigian.required' => 'Vui lòng nhập thời gian tổ chức.',
        'thoigian.date' => 'Thời gian không đúng định dạng ngày tháng.',

        'diadiem.required' => 'Vui lòng nhập địa điểm tổ chức.',

        'songuoithamdu.required' => 'Vui lòng nhập số người tham dự.',
        'songuoithamdu.integer' => 'Số người tham dự phải là số nguyên.',

        'sothanhvien.required' => 'Vui lòng nhập số thành viên tham gia.',
        'sothanhvien.integer' => 'Số thành viên phải là số nguyên.',

        'bienban.required' => 'Vui lòng tải lên tệp đính kèm.',
        'bienban.file' => 'Tệp đính kèm phải là tệp hợp lệ.'
    ];

    public function index()
    {
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function themHoiDong(Request $request)
    {
        $currentUser = auth()->user();

        $validator = Validator::make($request->all(), [
            'mahoidong' => 'required|unique:tblhoidong,PK_MaHoiDong',
            'madot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
            'machutri' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
            'mathuky' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
            'thoigian' => 'required|date',
            'diadiem' => 'required',
            'songuoithamdu' => 'required|integer',
            'sothanhvien' => 'required|integer',
            'bienban' => 'required|file',
            'sohd' => 'required'
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        if ($currentUser->FK_MaQuyen == 4) {
            $chiTietHD = new ChiTietHDModel();
            $chiTietHD->FK_MaLoaiHD = 1;
            $chiTietHD->FK_HinhThucHD = 1;
            $chiTietHD->save();

            $file = $request->file('bienban');
            $file->move(public_path('bienban/donvi/'), $file->getClientOriginalName());

            $filePath = 'bienban/donvi/' . $file->getClientOriginalName();
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $hoiDong = new HoiDongModel();
            $hoiDong->PK_MaHoiDong = $request->mahoidong;
            $hoiDong->FK_MaDot = $request->madot;
            $hoiDong->FK_MaTaiKhoan = $currentUser->PK_MaTaiKhoan;
            $hoiDong->dNgayTao = now();
            $hoiDong->FK_ChuTri = $request->machutri;
            $hoiDong->FK_ThuKy = $request->mathuky;
            $hoiDong->dThoiGianHop = $request->thoigian;
            $hoiDong->sDiaChi = $request->diadiem;
            $hoiDong->iSoNguoiThamDu = $request->songuoithamdu;
            $hoiDong->iSoThanhVien = $request->sothanhvien;
            $hoiDong->FK_ChiTietHD = $chiTietHD->PK_MaChiTietHD;
            $hoiDong->sDuongDan = $filePath;
            $hoiDong->sTenFile = $fileName;
            $hoiDong->sSoHD = $request->sohd;
            $hoiDong->sGhiChu = $request->sGhiChu;
            $hoiDong->save();
        }

        return response()->json([
            'message' => 'Thêm hội đồng thành công',
        ]);
    }

    public function capNhatHoiDong(Request $request)
    {
        $hoiDong = HoiDongModel::where('PK_MaHoiDong', '=', $request->mahoidong)->first();

        if (!$hoiDong) {
            return response()->json([
                'message' => 'Không tìm thấy hội đồng'
            ], 404);
        }

        

        return response()->json([
            'message' => 'Cập nhật hội đồng thành công',
            'data' => $request->all()
        ]);
    }

    public function layDanhSachHinhThucHD()
    {
        $listHinhThuc = HinhThucHDModel::all();
        return response()->json([
            'success' => true,
            'listHinhThuc' => $listHinhThuc
        ], 200);
    }

    public function layDanhSachLoaiHD()
    {
        $listLoaiHD = LoaiHDModel::all();
        return response()->json([
            'message' => 'success',
            'listLoaiHD' => $listLoaiHD
        ], 200);
    }
}
