<?php

namespace App\Http\Controllers;

use App\Models\KienToanModel;
use App\Models\NhiemVuModel;
use App\Models\ThanhVienHoiDongModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KienToanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = KienToanModel::with([
                'thanhVienHoiDong.taiKhoan.caNhan',
                'thanhVienHoiDong.nhiemVu',
            ])->get();
            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách kiện toàn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getListNhiemVu()
    {
        try {
            $nhiemvu = NhiemVuModel::all();
            return response()->json([
                'status' => true,
                'data' => $nhiemvu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy danh sách nhiệm vụ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function taoKienToan(Request $request)
    {
        try {
            // Log::info($request->all());
            $validator = Validator::make($request->all(), [
                'maKienToan' => 'required|unique:tblkientoan,PK_MaKienToan',
                'ngayTao' => 'required|date',
                'file' => 'required|file|mimes:pdf|max:2048',
                'thanhVien' => 'required|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $filePath = [];
            // Check if the file is valid and store it
            if ($request->file('file')) {
                $file = $request->file('file');
                $storedPath = $file->store('kientoan/');
                $filePath = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $storedPath,
                ];
            }

            $kienToan = KienToanModel::create([
                'PK_MaKienToan' => $request->maKienToan,
                'dNgayTao' => $request->ngayTao,
                'sTenFile' => $filePath['name'],
                'sDuongDan' => $filePath['path'],
                'bTrangThai' => 0,
            ]);

            Log::info($kienToan);

            $listThanhVien = json_decode($request->thanhVien, true);
            foreach ($listThanhVien as $thanhVien) {
                ThanhVienHoiDongModel::create([
                    'FK_MaKienToan' => $kienToan->PK_MaKienToan,
                    'FK_TaiKhoan' => $thanhVien['FK_MaTaiKhoan'],
                    'FK_MaNhiemVu' => $thanhVien['nhiemVu'],
                ]);
            }

            DB::commit();


            return response()->json([
                'status' => true,
                'message' => 'Tạo kiến toán thành công',
                'data' => $kienToan
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating KienToan: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);
            // Log the error message for debugging
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo kiến toán: ' . $e->getMessage()
            ], 500);
        }
    }

    public function suaKienToan(Request $request)
    {
        try {
            Log::info($request->all());
            $validator = Validator::make($request->all(), [
                'maKienToan' => 'required|exists:tblkientoan,PK_MaKienToan',
                'ngayTao' => 'required|date',
                'file' => 'nullable|file|mimes:pdf|max:2048',
                'thanhVien' => 'required|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $kienToan = KienToanModel::find($request->maKienToan);
            if (!$kienToan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kiến toán không tồn tại'
                ], 404);
            }

            // Check if the file is valid and store it
            if ($request->file('file')) {
                $file = $request->file('file');
                $storedPath = $file->store('kientoan/');
                $filePath = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $storedPath,
                ];

                // Xóa file cũ nếu có
                if (Storage::exists($kienToan->sDuongDan)) {
                    Storage::delete($kienToan->sDuongDan);
                }
                $kienToan->sTenFile = $filePath['name'];
                $kienToan->sDuongDan = $filePath['path'];
            }

            $kienToan->PK_MaKienToan = $request->maKienToan;
            $kienToan->dNgayTao = $request->ngayTao;
            $kienToan->save();

            // Xóa các thành viên cũ trước khi thêm mới
            ThanhVienHoiDongModel::where('FK_MaKienToan', $request->maKienToan)->delete();

            // Thêm các thành viên mới
            $listThanhVien = json_decode($request->thanhVien, true);
            foreach ($listThanhVien as $thanhVien) {
                ThanhVienHoiDongModel::create([
                    'FK_MaKienToan' => $kienToan->PK_MaKienToan,
                    'FK_TaiKhoan' => $thanhVien['FK_MaTaiKhoan'],
                    'FK_MaNhiemVu' => $thanhVien['nhiemVu'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật kiến toán thành công',
                'data' => $kienToan
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating KienToan: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật kiến toán: ' . $e->getMessage()
            ], 500);
        }
    }

    public function xoaKienToan($id) {
        try {
            $kienToan = KienToanModel::find($id);
            if (!$kienToan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kiện toàn không tồn tại'
                ], 404);
            }

            $existingHoiDongTruong = KienToanModel::where('PK_MaKienToan', $id)
                ->whereHas('hoiDongTruong', function () {})
                ->first();

            Log::info($existingHoiDongTruong);
            if($existingHoiDongTruong) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa kiện toàn này vì đã được sử dụng'
                ], 400);
            }

            // Xóa file nếu có
            if (Storage::exists($kienToan->sDuongDan)) {
                Storage::delete($kienToan->sDuongDan);
            }

            $kienToan->thanhVienHoiDong()->delete();

            $kienToan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa kiến toán thành công'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting KienToan: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa kiến toán: ' . $e->getMessage()
            ], 500);
        }

    }

    public function capNhatTrangThai($id)
    {
        try {
            $kienToan = KienToanModel::find($id);
            if (!$kienToan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kiến toán không tồn tại'
                ], 404);
            }

            $kienToanActive = KienToanModel::where('bTrangThai', 1)->first();
            if ($kienToanActive) {
                $kienToanActive->bTrangThai = 0;
                $kienToanActive->save();
            }

            $kienToan->bTrangThai = !$kienToan->bTrangThai;
            $kienToan->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $kienToan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    public function layKienToanActive() {
        try {
            $kienToanActive = KienToanModel::where('bTrangThai', 1)
            ->with('thanhVienHoiDong')
            ->first();
            if (!$kienToanActive) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy kiện toàn đang hoạt động'
                ], 404);
            }

            $kienToanActive['soThanhVien'] = $kienToanActive->thanhVienHoiDong->count();

            return response()->json([
                'status' => true,
                'data' => $kienToanActive
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy kiện toàn đang hoạt động: ' . $e->getMessage()
            ], 500);
        }
    }
}
