<?php

namespace App\Http\Controllers;

use App\Models\DeXuatModel;
use App\Models\DotTDKTModel;
use App\Models\HinhThucModel;
use App\Models\HoiDongModel;
use App\Models\LoaiHDModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        $hanNopBienBan = $dotActive->dHanBienBanDonVi;
        $homNay = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        if ($hanNopBienBan < $homNay)  {
            return response()->json([
                'success' => false,
                'error' => ['Hết thời gian nộp biên bản']
            ], 422);
        }

        if ($currentUser->FK_MaQuyen == 3) {
            $validator = Validator::make($request->all(), [
                'maHoiDong' => 'required',
                'maDot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
                'huongDanSo' => 'required',
                'thoiGian' => 'required|date',
                'diaChi' => 'required',
                'tongNguoiTrieuTap' => 'required|integer',
                'soNguoiCoMat' => 'required|integer',
                'ghiChu' => 'nullable|string',
                'chuTichId' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
                'phoThuongTrucId' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
                'phoChuTichId' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
                'thuKyId' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
                'fileBienBan' => 'nullable|file',
                'fileKiemPhieu' => 'nullable|file',
                'maDotXuat' => 'nullable|exists:tbldotxuat,PK_MaDotXuat',
            ], $this->messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }

            $this->themHoiDongTruong($request, $currentUser);
        } else if ($currentUser->FK_MaQuyen == 4) {
            $validator = Validator::make($request->all(), [
                'mahoidong' => 'required',
                'madot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
                'machutri' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
                'mathuky' => 'required|exists:tbltaikhoan,PK_MaTaiKhoan',
                'thoigian' => 'required|date',
                'diadiem' => 'required',
                'hinhthuchoidong' => 'required|exists:tblhinhthuc,PK_MaHinhThuc',
                'maloaihoidong' => 'required|exists:tblloaihoidong,PK_MaLoaiHD',
                'songuoithamdu' => 'required|integer',
                'sothanhvien' => 'required|integer',
                'bienban' => 'nullable|file',
                'sohd' => 'required',
            ], $this->messages);


            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }
            $this->themHoiDongDV($request, $currentUser);
        }

        return response()->json([
            'message' => 'Thêm hội đồng thành công',
        ]);
    }

    public function themHoiDongDV(Request $request, $currentUser)
    {
        $existingHoiDong = HoiDongModel::where('PK_MaHoiDong', $request->mahoidong)->first();
        if (!$existingHoiDong) {
            $file = $request->file('bienban');
            $folderDot = $request->madot;
            $filePath = $file->store('vanBanHoiDong/' . $folderDot);

            $hoiDong = new HoiDongModel();
            $hoiDong->PK_MaHoiDong = $request->mahoidong;
            $hoiDong->FK_MaDot = $request->madot;
            $hoiDong->FK_MaTaiKhoan = $currentUser->PK_MaTaiKhoan;
            $hoiDong->dNgayTao = getDateNow();
            $hoiDong->FK_ChuTri = $request->machutri;
            $hoiDong->FK_ThuKy = $request->mathuky;
            $hoiDong->dThoiGianHop = $request->thoigian;
            $hoiDong->sDiaChi = $request->diadiem;
            $hoiDong->iSoNguoiThamDu = $request->songuoithamdu;
            $hoiDong->iSoThanhVien = $request->sothanhvien;
            $hoiDong->FK_MaLoaiHD = 1;
            $hoiDong->FK_MaHinhThuc = 1;
            $hoiDong->sDuongDanBienBan = $filePath;
            $hoiDong->sTenBienBan = $file->getClientOriginalName();
            $hoiDong->sSoHD = $request->sohd;
            $hoiDong->sGhiChu = $request->ghichu;
            $hoiDong->save();

        } else {
            $existingHoiDong->update([
                'FK_MaTaiKhoan' => $currentUser->PK_MaTaiKhoan,
                'dNgayTao' => getDateNow(),
                'FK_ChuTri' => $request->machutri,
                'FK_ThuKy' => $request->mathuky,
                'dThoiGianHop' => $request->thoigian,
                'sDiaChi' => $request->diadiem,
                'iSoNguoiThamDu' => $request->songuoithamdu,
                'iSoThanhVien' => $request->sothanhvien,
                'sSoHD' => $request->sohd,
                'sGhiChu' => $request->ghichu
            ]);

            // DeXuatModel::where('FK_MaHoiDong', $existingHoiDong->PK_MaHoiDong)->delete();
            // $caNhan = json_decode($request->dexuatcanhan, true);
            // foreach ($caNhan as $key => $cn) {
            //     foreach ($cn as $key2 => $value) {
            //         // Log::info($value);
            //         $dexuat = new DeXuatModel();
            //         $dexuat->FK_User = $value['taiKhoan'];
            //         $dexuat->FK_MaHoiDong = $request->mahoidong;
            //         $dexuat->iSoNguoiBau = $value['soPhieu'];
            //         $dexuat->dNgayTao = now();
            //         $dexuat->FK_MaDanhHieu = $key;
            //         $dexuat->save();
            //     }
            // }

            // $donVi = json_decode($request->dexuatdonvi, true);
            // foreach ($donVi as $key => $dv) {
            //     $dexuat = new DeXuatModel();
            //     $dexuat->FK_User = $currentUser->PK_MaTaiKhoan;
            //     $dexuat->FK_MaHoiDong = $request->mahoidong;
            //     $dexuat->iSoNguoiBau = $dv['soPhieu'];
            //     $dexuat->dNgayTao = now();
            //     $dexuat->FK_MaDanhHieu = $dv['id'];
            //     $dexuat->save();
            // }


            if ($request->hasFile('bienban')) {
                if($existingHoiDong->sDuongDan != null) {
                    if (Storage::exists($existingHoiDong->sDuongDan)) {
                        Storage::delete($existingHoiDong->sDuongDan);
                    }
                }
                $file = $request->file('bienban');
                $filePath = $file->store('vanBanHoiDong/' . $request->madot);
                $existingHoiDong->sDuongDanBienBan = $filePath;
                $existingHoiDong->sTenBienBan = $file->getClientOriginalName();
                $existingHoiDong->save();
            }
        }
    }
    public function themHoiDongTruong(Request $request, $currentUser)
    {

        $existingHoiDong = HoiDongModel::where('PK_MaHoiDong', $request->maHoiDong)->first();
        if (!$existingHoiDong) {
            $fileBienBan = $request->file('fileBienBan');
            $folderDot = $request->madot;
            $filePathBienBan = $fileBienBan->store('vanBanHoiDong/' . $folderDot);

            $fileKiemPhieu = $request->file('fileKiemPhieu');
            $filePathKiemPhieu = $fileKiemPhieu->store('vanBanHoiDong/' . $folderDot);


            $hoiDong = new HoiDongModel();
            $hoiDong->PK_MaHoiDong = $request->maHoiDong;
            $hoiDong->FK_MaDot = $request->maDot;
            $hoiDong->FK_MaTaiKhoan = $currentUser->PK_MaTaiKhoan;
            $hoiDong->dNgayTao = getDateNow();
            $hoiDong->FK_ChuTri = $request->chuTichId;
            $hoiDong->FK_ThuKy = $request->thuKyId;
            $hoiDong->dThoiGianHop = $request->thoiGian;
            $hoiDong->sDiaChi = $request->diaChi;
            $hoiDong->iSoNguoiThamDu = $request->soNguoiCoMat;
            $hoiDong->iSoThanhVien = $request->tongNguoiTrieuTap;
            // $hoiDong->FK_ChiTietHD = $chiTietHD->PK_MaChiTietHD;
            $hoiDong->sDuongDanBienBan = $filePathBienBan;
            $hoiDong->sTenBienBan = $fileBienBan->getClientOriginalName();
            $hoiDong->sDuongDanKiemPhieu = $filePathKiemPhieu;
            $hoiDong->sTenKiemPhieu = $fileKiemPhieu->getClientOriginalName();
            $hoiDong->sSoHD = $request->huongDanSo;
            $hoiDong->sGhiChu = $request->ghiChu;
            $hoiDong->FK_PhoChuTich = $request->phoChuTichId;
            $hoiDong->FK_PhoChuTichTT = $request->phoThuongTrucId;
            $hoiDong->FK_MaLoaiHD = 2;
            $hoiDong->FK_MaHinhThuc = 1;
            $hoiDong->FK_MaDotXuat = $request->maDotXuat;
            $hoiDong->save();
        } else {
            $existingHoiDong->update([
                'FK_MaTaiKhoan' => $currentUser->PK_MaTaiKhoan,
                'dNgayTao' => getDateNow(),
                'FK_ChuTri' => $request->chuTichId,
                'FK_ThuKy' => $request->thuKyId,
                'dThoiGianHop' => $request->thoiGian,
                'sDiaChi' => $request->diaChi,
                'iSoNguoiThamDu' => $request->soNguoiCoMat,
                'iSoThanhVien' => $request->tongNguoiTrieuTap,
                'sSoHD' => $request->huongDanSo,
                'sGhiChu' => $request->ghiChu,
                'FK_PhoChuTich' => $request->phoChuTichId,
                'FK_PhoChuTichTT' => $request->phoThuongTrucId
            ]);
            if ($request->hasFile('fileBienBan')) {
                if ($existingHoiDong->sDuongDanBienBan != null) {
                    if (Storage::exists($existingHoiDong->sDuongDanBienBan)) {
                        Storage::delete($existingHoiDong->sDuongDanBienBan);
                    }
                }
                $file = $request->file('fileBienBan');
                $filePath = $file->store('vanBanHoiDong/' . $request->maDot);
                $existingHoiDong->sDuongDanBienBan = $filePath;
                $existingHoiDong->sTenBienBan = $file->getClientOriginalName();
                $existingHoiDong->save();
            }
            if ($request->hasFile('fileKiemPhieu')) {
                if ($existingHoiDong->sDuongDanKiemPhieu != null) {
                    if (Storage::exists($existingHoiDong->sDuongDanKiemPhieu)) {
                        Storage::delete($existingHoiDong->sDuongDanKiemPhieu);
                    }
                }
                $file = $request->file('fileKiemPhieu');
                $filePath = $file->store('vanBanHoiDong/' . $request->maDot);
                $existingHoiDong->sDuongDanKiemPhieu = $filePath;
                $existingHoiDong->sTenKiemPhieu = $file->getClientOriginalName();
                $existingHoiDong->save();
            }
        }
    }

    public function capNhatHoiDong(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mahoidong' => 'required|exists:tblhoidong,PK_MaHoiDong',
            'diadiem' => 'required',
            'songuoithamdu' => 'required|integer',
            'sothanhvien' => 'required|integer',
            'bienban' => 'required|file',
            'sohd' => 'required'
        ]);

        $hoiDong = HoiDongModel::where('PK_MaHoiDong', '=', $request->mahoidong)->first();

        if (!$hoiDong) {
            return response()->json([
                'message' => 'Không tìm thấy hội đồng'
            ], 404);
        }

        File::delete(public_path($hoiDong->sDuongDan));

        $file = $request->file('bienban');
        $file->move(public_path('bienban/donvi/'), $file->getClientOriginalName());

        $filePath = 'bienban/donvi/' . $file->getClientOriginalName();
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $hoiDong->update([
            'sDiaChi' => $request->diadiem,
            'iSoNguoiThamDu' => $request->songuoithamdu,
            'iSoThanhVien' => $request->sothanhvien,
            'sDuongDan' => $filePath,
            'sTenFile' => $fileName,
            'sSoHD' => $request->sohd,
            'sGhiChu' => $request->ghichu
        ]);

        return response()->json([
            'message' => 'Cập nhật hội đồng thành công',
        ]);
    }

    public function layDanhSachHinhThucHD()
    {
        $listHinhThuc = HinhThucModel::all();
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

    public function layThongTinHoiDong()
    {
        $currentUser = auth()->user();
        $dotActive = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActive) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đợt thi đua'
            ], 404);
        }
        $hoidong = HoiDongModel::where('FK_MaTaiKhoan', $currentUser->PK_MaTaiKhoan)
            ->with(['dexuat' => function ($query) {
                $query->with(['danhhieu']);
            }, 'dexuat.taiKhoan' => function ($query) {
                $query->with(['caNhan']);
            }])
            ->where('FK_MaDot', $dotActive->PK_MaDot)
            ->first();

        if (!$hoidong) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hội đồng'
            ], 404);
        }

        // Organize proposals into individual and organizational categories
        $deXuatCaNhan = [];
        $deXuatDonVi = [];

        if ($hoidong->dexuat && count($hoidong->dexuat) > 0) {
            foreach ($hoidong->dexuat as $deXuat) {
                if ($deXuat->danhhieu) {
                    // Check if the award is for individual or organization
                    if ($deXuat->danhhieu->FK_MaLoaiDanhHieu == 1) { // Assuming 1 is for individual
                        $deXuatCaNhan[] = $deXuat;
                    } else if ($deXuat->danhhieu->FK_MaLoaiDanhHieu == 2) { // Assuming 2 is for organization
                        $deXuatDonVi[] = $deXuat;
                    }
                }
            }
        }

        // Add the categorized proposals to the response data
        $hoidong->deXuatCaNhan = $deXuatCaNhan;
        $hoidong->deXuatDonVi = $deXuatDonVi;

        return response()->json([
            'success' => true,
            'data' => $hoidong
        ], 200);
    }
}
