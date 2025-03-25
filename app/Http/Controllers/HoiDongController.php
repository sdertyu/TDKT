<?php

namespace App\Http\Controllers;

use App\Models\HinhThucHDModel;
use App\Models\LoaiHDModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        'duongdan.required' => 'Vui lòng tải lên tệp đính kèm.',
        'duongdan.file' => 'Tệp đính kèm phải là tệp hợp lệ.'
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
            'madot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
            'machutri' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
            'mathuky' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
            'thoigian' => 'required|date',
            'diadiem' => 'required',
            'songuoithamdu' => 'required|integer',
            'sothanhvien' => 'required|integer',
            'duongdan' => 'required|file'
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'message' => 'Thêm hội đồng thành công',
            'người tạo' => $currentUser
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
