<?php

namespace App\Http\Controllers;

use App\Models\DotTDKTModel;
use App\Models\DotXuatModel;
use App\Models\HoiDongModel;
use App\Models\ThongBaoModel;
use App\Models\ThongBaoQuyenModel;
use App\Models\VBDKModel;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use ZipStream\ZipStream;

class DotTDKTController extends Controller
{
    protected $messages = [
        'ngaytao.required' => 'Bạn cần chọn ngày tạo đợt.',
        'ngaytao.date' => 'Ngày tạo phải là một ngày hợp lệ.',
        'ngaytao.after_or_equal' => 'Ngày tạo phải không được nhỏ hơn ngày hiện tại.',
    ];

    public function index()
    {
        $listDotTDKT = DotTDKTModel::orderBy("PK_MaDot", "DESC")->get();
        return response()->json([
            'status' => 'success',
            'data' => $listDotTDKT
        ], 200);
    }
    public function themDotTDKT(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'iNamBatDau' => 'required|numeric|min:1933|unique:tbldotthiduakhenthuong,iNamBatDau|max:' . Carbon::now()->year,
            'dHanBienBanDonVi' => 'nullable|date',
            'dHanNopMinhChung' => 'nullable|date',
            'dHanBienBanHoiDong' => 'nullable|date',
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $carbon = Carbon::now();  // Tạo đối tượng Carbon với thời gian hiện tại
        $carbon->setTimezone('Asia/Ho_Chi_Minh');  // Đặt múi giờ là múi giờ Việt Nam
        $dotTDKT = DotTDKTModel::create([
            'PK_MaDot' => $request->iNamBatDau . '-' . ($request->iNamBatDau + 1),
            'iNamBatDau' => $request->iNamBatDau,
            'iNamKetThuc' => $request->iNamBatDau + 1,
            'bTrangThai' => 0,
            'dNgayTao' => $carbon->format('Y-m-d H:i:s'),
            'dHanBienBanDonVi' => $request->dHanBienBanDonVi,
            'dHanBienBanHoiDong' => $request->dHanBienBanHoiDong,
            'dHanNopMinhChung' => $request->dHanNopMinhChung,
        ]);

        $fields = [
            'dHanBienBanDonVi' => [
                'tieuDe' => 'Thông báo về thời hạn nộp biên bản đơn vị',
                'noiDungLabel' => 'Yêu cầu các đơn vị nộp biên bản bình bầu thi đua khen thưởng của đơn vị mình lên hệ thống trước: ',
                'quyen' => [4],
            ],
            'dHanBienBanHoiDong' => [
                'tieuDe' => 'Thông báo về thời hạn nộp biên bản hội đồng',
                'noiDungLabel' => 'Hội đồng thi đua cần hoàn thành biên bản họp xét thi đua và nộp lên hệ thống trước: ',
                'quyen' => [3],
            ],
            'dHanNopMinhChung' => [
                'tieuDe' => 'Thông báo về hạn nộp minh chứng',
                'noiDungLabel' => 'Đề nghị các đơn vị cùng toàn bộ cá nhân trong trường tiến hành cung cấp minh chứng về các danh hiệu đã được đề xuất theo biên bản bình xét thi đua tại đơn vị trước: ',
                'quyen' => [4, 5],
            ],
        ];

        foreach ($fields as $field => $data) {
            if ($request->$field !== null) {
                $thongBao = ThongBaoModel::create([
                    'sTieuDe' => $data['tieuDe'],
                    'sNoiDung' => $data['noiDungLabel'] . formatDate($request->$field),
                    'dNgayTao' => $carbon->format('Y-m-d H:i:s'),
                    'FK_NguoiTao' => auth()->user()->PK_MaTaiKhoan,
                ]);

                foreach ($data['quyen'] as $maQuyen) {
                    ThongBaoQuyenModel::create([
                        'FK_MaThongBao' => $thongBao->PK_MaThongBao,
                        'FK_MaQuyen' => $maQuyen,
                    ]);
                }
            }
        }

        if ($dotTDKT) {
            return response()->json([
                'message' => 'Thêm đợt thị đua',
                'data' => $dotTDKT
            ], 200);
        } else {
            return response()->json([
                'message' => 'Không thêm đợt thị đua'
            ], 404);
        }
    }

    public function SuaDotTDKT(Request $request)
    {
        $rules = [
            'dHanBienBanDonVi' => 'nullable|date',
            'dHanNopMinhChung' => 'nullable|date',
            'dHanBienBanHoiDong' => 'nullable|date',
        ];

        $messages = [
            'dHanBienBanDonVi.date' => 'Ngày không hợp lệ',
            'dHanNopMinhChung.date' => 'Ngày không hợp lệ',
            'dHanBienBanHoiDong.date' => 'Ngày không hợp lệ',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $dotTDKT = DotTDKTModel::where('PK_MaDot', '=', $request->PK_MaDot)->first();

            if (!$dotTDKT) {
                return response()->json([
                    'message' => 'Không tìm thấy đợt thi đua'
                ], 404);
            }

            // Kiểm tra thay đổi & tạo thông báo
            $fields = [
                'dHanBienBanDonVi' => [
                    'tieuDe' => 'Thông báo về thời hạn nộp biên bản đơn vị',
                    'noiDungLabel' => 'Yêu cầu các đơn vị nộp biên bản bình bầu thi đua khen thưởng của đơn vị mình lên hệ thống trước: ',
                    'quyen' => [4],
                ],
                'dHanBienBanHoiDong' => [
                    'tieuDe' => 'Thông báo về thời hạn nộp biên bản hội đồng',
                    'noiDungLabel' => 'Hội đồng thi đua cần hoàn thành biên bản họp xét thi đua và nộp lên hệ thống trước: ',
                    'quyen' => [3],
                ],
                'dHanNopMinhChung' => [
                    'tieuDe' => 'Thông báo về hạn nộp minh chứng',
                    'noiDungLabel' => 'Đề nghị các đơn vị cùng toàn bộ cá nhân trong trường tiến hành cung cấp minh chứng về các danh hiệu đã được đề xuất theo biên bản bình xét thi đua tại đơn vị trước: ',
                    'quyen' => [4, 5],
                ],
            ];

            $carbon = Carbon::now();  // Tạo đối tượng Carbon với thời gian hiện tại
            $carbon->setTimezone('Asia/Ho_Chi_Minh');  // Đặt múi giờ là múi giờ Việt Nam

            foreach ($fields as $field => $data) {
                if ($dotTDKT->$field !== $request->$field) {
                    $thongBao = ThongBaoModel::create([
                        'sTieuDe' => $data['tieuDe'],
                        'sNoiDung' => $data['noiDungLabel'] . formatDate($request->$field),
                        'dNgayTao' => $carbon->format('Y-m-d H:i:s'),
                        'FK_NguoiTao' => auth()->user()->PK_MaTaiKhoan,
                    ]);

                    foreach ($data['quyen'] as $maQuyen) {
                        ThongBaoQuyenModel::create([
                            'FK_MaThongBao' => $thongBao->PK_MaThongBao,
                            'FK_MaQuyen' => $maQuyen,
                        ]);
                    }
                }
            }


            // Gán dữ liệu mới
            $dotTDKT->dHanBienBanDonVi = $request->dHanBienBanDonVi;
            $dotTDKT->dHanNopMinhChung = $request->dHanNopMinhChung;
            $dotTDKT->dHanBienBanHoiDong = $request->dHanBienBanHoiDong;
            $dotTDKT->save();

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $dotTDKT
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật đợt TĐKT: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.'
            ], 500);
        }
    }


    public function XoaDotTDKT($id)
    {
        $dot = DotTDKTModel::where('PK_MaDot', '=', $id)->first();
        if (!$dot) {
            return response()->json([
                'message' => 'Không tìm thấy đợt thi đua'
            ], 404);
        }

        $DotXuat = DotXuatModel::where('FK_MaDot', '=', $id)->exists();
        $hoiDong = HoiDongModel::where('FK_MaDot', '=', $id)->exists();

        if ($DotXuat || $hoiDong) {
            return response()->json([
                'message' => 'Không thể xóa đợt thi đua vì đã có đột xuất hoặc hội đồng liên quan.'
            ], 400);
        }

        try {
            // Xóa văn bản đính kèm
            VBDKModel::where('FK_MaDot', $id)->delete();

            // Xóa đợt thi đua
            $dot->delete();

            return response()->json([
                'message' => 'Đã xóa toàn bộ văn bản đính kèm và đợt thi đua.'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xóa.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function suaTrangThaiDot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'PK_MaDot' => 'required',
            'bTrangThai' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $updateDotTDKT = DotTDKTModel::where('PK_MaDot', '=', $request->PK_MaDot)->first();
        if ($request->bTrangThai == 1) {
            $currentActive = DotTDKTModel::where('bTrangThai', '=', 1)->first();
            if ($currentActive) {
                $currentActive->bTrangThai = 0;
                $currentActive->save();
            }
        }

        if ($updateDotTDKT) {
            $updateDotTDKT->bTrangThai = $request->bTrangThai;
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
        ], 200);
    }

    public function layDotActive()
    {
        $dotTDKT = DotTDKTModel::where('bTrangThai', '=', 1)->with('vanbandinhkem')->first();

        return response()->json([
            'message' => 'Thành công',
            'data' => $dotTDKT
        ], 200);
    }

    public function themVanBanDinhKem(Request $request)
    {
        $this->messages = [
            'file.*.required' => 'Bạn chưa chọn file',
            'file.*.file' => 'Vui lòng nhập đúng file',
            'file.*.mimes' => 'Định dạng file không hợp lệ.',
            'tenvanban.*.required' => 'Vui lòng nhập tên văn bản'
        ];

        $validator = Validator::make($request->all(), [
            'madot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
            'tenvanban.*' => 'required',
            'file.*' => 'required',
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'Không tìm thấy file tải lên'
            ], 404);
        }

        $filePaths = [];
        $dotTDKT = DotTDKTModel::where('PK_MaDot', '=', $request->madot)->first();
        $folderDot = $dotTDKT->iNamBatDau . '-' . $dotTDKT->iNamKetThuc;

        foreach ($request->file('file') as $file) {
            // $filePath = $file->move('vanbandinhkem/' . $folderDot, $file->getClientOriginalName());
            $storedPath = $file->store('vanbandinhkem/' . $folderDot);

            $filePaths[] = [
                'path' => $storedPath,
                'name' => $file->getClientOriginalName(), // Tên gốc để lưu vào DB
            ];
        }

        $response = [];

        foreach ($filePaths as $index => $file) {
            $vanBanDinhKem = new VBDKModel();
            $vanBanDinhKem->FK_MaDot = $request->madot;
            $vanBanDinhKem->sTenVanBan = $request->tenvanban[$index];
            $vanBanDinhKem->sTenFile = $file['name'];
            $vanBanDinhKem->dNgayTao = now();
            $vanBanDinhKem->sDuongDan = $file['path'];
            $vanBanDinhKem->save();
            $response[] = $vanBanDinhKem;
        }

        return response()->json([
            'message' => 'Thêm thành công',
            'data' => $response
        ], 200);
    }

    public function suaVanBanDinhKem(Request $request)
    {
        $this->messages = [
            'tenvanban.required' => 'Vui lòng nhập tên văn bản'

        ];

        $validator = Validator::make($request->all(), [
            'tenvanban' => 'required',
            'mavanban' => 'required|exists:tblvanbandinhkem,PK_MaVanBan'
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        $vanBanDinhKem = VBDKModel::where('PK_MaVanBan', '=', $request->mavanban)->first();
        if (!$vanBanDinhKem) {
            return response()->json([
                'message' => 'Không tìm thấy văn bản đính kèm'
            ], 404);
        }

        if ($request->hasFile('file')) {
            $path = $vanBanDinhKem->sDuongDan;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            $file = $request->file('file');
            $folderDot = $request->madot;
            $filePath = $file->store('vanbandinhkem/' . $folderDot);

            $vanBanDinhKem->sTenVanBan = $request->tenvanban;
            $vanBanDinhKem->sTenFile = $file->getClientOriginalName();
            $vanBanDinhKem->sDuongDan = $filePath;
            $vanBanDinhKem->save();

            return response()->json([
                'message' => 'Cập nhật thành công'
            ], 200);
        } else {
            $vanBanDinhKem->sTenVanBan = $request->tenvanban;
            $vanBanDinhKem->save();

            return response()->json([
                'message' => 'Cập nhật thành công'
            ], 200);
        }
    }

    public function xoaVanBanDinhKem($id)
    {
        $vanBanDinhKem = VBDKModel::where('PK_MaVanBan', '=', $id)->first();
        if (!$vanBanDinhKem) {
            return response()->json([
                'message' => 'Không tìm thấy văn bản đính kèm'
            ], 404);
        }

        $path = $vanBanDinhKem->sDuongDan;

        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        $vanBanDinhKem->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ], 200);
    }

    public function layDanhSachVanBanDinhKem($id)
    {
        $listVanBanDinhKem = VBDKModel::where('FK_MaDot', '=', $id)->get();

        foreach ($listVanBanDinhKem as $item) {
            $item->sDuongDan = asset($item->sDuongDan);
        }

        return response()->json([
            'message' => 'Thành công',
            'data' => $listVanBanDinhKem
        ]);
    }

    public function layDanhSachVanBanDinhKemActive()
    {
        $dotTDKT = DotTDKTModel::where('bTrangThai', '=', 1)->first();
        if ($dotTDKT) {
            $listVanBanDinhKem = VBDKModel::where('FK_MaDot', '=', $dotTDKT->PK_MaDot)->get();

            foreach ($listVanBanDinhKem as $item) {
                $item->sDuongDan = asset($item->sDuongDan);
            }

            return response()->json([
                'message' => 'Thành công',
                'data' => $listVanBanDinhKem
            ]);
        } else {
            return response()->json([
                'message' => 'Không tìm thấy đợt thi đua nào đang hoạt động',
            ], 404);
        }
    }

    public function viewVbdk($id)
    {
        $vanBan = VBDKModel::findOrFail($id);
        $filePath = storage_path('app/' . $vanBan->sDuongDan);

        abort_unless(file_exists($filePath), 404, 'Không tìm thấy file');

        $mime = mime_content_type($filePath);
        abort_unless($mime === 'application/pdf', 415, 'Chỉ hỗ trợ file PDF');

        return response()->file($filePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $vanBan->sTenFile . '"'
        ]);
    }

    public function downloadVbdk($id)
    {
        $vanban = VBDKModel::findOrFail($id);

        // Đường dẫn thực trong storage/app
        $path = $vanban->sDuongDan;

        if (!Storage::exists($path)) {
            return response()->json(['message' => 'Không tìm thấy file'], 404);
        }

        // Trả file download, giữ tên gốc
        return Storage::download($path, $vanban->sTenFile);
    }

    public function downloadZipVanBan($madot)
    {
        $vanbans = VBDKModel::where('FK_MaDot', $madot)->get();

        if ($vanbans->isEmpty()) {
            return response()->json(['message' => 'Không có văn bản nào để tải.'], 404);
        }

        // Tên zip
        $dot = DotTDKTModel::find($madot)->first();
        $zipName = 'vanban-' . $dot->PK_MaDot . '.zip';

        // Tạo stream zip và trả về response
        return response()->streamDownload(function () use ($vanbans) {
            $zip = new ZipStream();

            foreach ($vanbans as $vb) {
                if (Storage::exists($vb->sDuongDan)) {
                    $stream = Storage::readStream($vb->sDuongDan);
                    $zip->addFileFromStream($vb->sTenFile, $stream);
                    fclose($stream);
                }
            }

            $zip->finish();
        }, $zipName, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $zipName . '"',
        ]);
    }

    //đợt
    public function layDanhSachDotDotXuat($maDot)
    {
        try {
            $listDotTDKT = DotXuatModel::where('FK_MaDot', $maDot)
                ->orderBy("dNgayBatDau", "DESC")->get();
            return response()->json([
                'status' => 'success',
                'data' => $listDotTDKT
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách đợt đột xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function themDotDotXuat(Request $request)
    {
        $this->messages = [
            'maDot.required' => 'Bạn chưa chọn đợt thi đua',
            'maDot.exists' => 'Đợt thi đua không tồn tại',

            'ngayBatDau.required' => 'Bạn chưa chọn ngày bắt đầu',
            'ngayBatDau.date' => 'Ngày bắt đầu không hợp lệ',
            'ngayBatDau.after_or_equal' => 'Ngày bắt đầu phải lớn hơn hoặc bằng ngày hiện tại',

            'ngayKetThuc.required' => 'Bạn chưa chọn ngày kết thúc',
            'ngayKetThuc.date' => 'Ngày kết thúc không hợp lệ',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',

            'hanNopMinhChung.required' => 'Bạn chưa chọn hạn nộp minh chứng',
            'hanNopMinhChung.date' => 'Hạn nộp minh chứng không hợp lệ',

            'hanNopBienBan.required' => 'Bạn chưa chọn hạn nộp biên bản',
            'hanNopBienBan.date' => 'Hạn nộp biên bản không hợp lệ',

            'hanNopDeXuat.required' => 'Bạn chưa chọn hạn nộp đề xuất',
            'hanNopDeXuat.date' => 'Hạn nộp đề xuất không hợp lệ',
        ];


        $validator = Validator::make($request->all(), [
            'maDot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
            'ngayBatDau' => 'required|date|after_or_equal:today',
            'ngayKetThuc' => 'required|date|after_or_equal:ngayBatDau',
            'hanNopMinhChung' => 'required|date',
            'hanNopBienBan' => 'required|date',
            'hanNopDeXuat' => 'required|date',
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        try {
            $dotTDKT = DotXuatModel::create([
                'PK_MaDotXuat' => $request->maDot . '-' . $request->ngayBatDau,
                'FK_MaDot' => $request->maDot,
                'dNgayBatDau' => $request->ngayBatDau,
                'dNgayKetThuc' => $request->ngayKetThuc,
                'dHanNopMinhChung' => $request->hanNopMinhChung,
                'dHanNopDeXuat' => $request->hanNopDeXuat,
                'dHanBienBanHoiDong' => $request->hanNopBienBan,
                'bTrangThai' => 0,
            ]);

            return response()->json([
                'message' => 'Thêm đợt đột xuất thành công',
                'data' => $dotTDKT
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi thêm đợt đột xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function suaDotDotXuat($maDotXuat, Request $request)
    {
        $dotDotXuat = DotXuatModel::find($maDotXuat);
        if (!$dotDotXuat) {
            return response()->json([
                'message' => 'Không tìm thấy đợt đột xuất'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'ngayBatDau' => 'required|date|after_or_equal:today',
            'ngayKetThuc' => 'required|date|after_or_equal:ngayBatDau',
            'hanNopMinhChung' => 'required|date',
            'hanNopBienBan' => 'required|date',
            'hanNopDeXuat' => 'required|date',
        ], $this->messages);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }
        try {
            $dotDotXuat->dNgayBatDau = $request->ngayBatDau;
            $dotDotXuat->dNgayKetThuc = $request->ngayKetThuc;
            $dotDotXuat->dHanNopMinhChung = $request->hanNopMinhChung;
            $dotDotXuat->dHanNopDeXuat = $request->hanNopDeXuat;
            $dotDotXuat->dHanBienBanHoiDong = $request->hanNopBienBan;
            $dotDotXuat->save();

            return response()->json([
                'message' => 'Cập nhật đợt đột xuất thành công',
                'data' => $dotDotXuat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật đợt đột xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function xoaDotDotXuat($maDotXuat)
    {
        $dotDotXuat = DotXuatModel::find($maDotXuat);
        if (!$dotDotXuat) {
            return response()->json([
                'message' => 'Không tìm thấy đợt đột xuất'
            ], 404);
        }

        try {
            $dotDotXuat->delete();

            return response()->json([
                'message' => 'Đã xóa đợt đột xuất thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xóa đợt đột xuất: '
            ], 500);
        }
    }

    public function capNhatTrangThaiDotDotXuat($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trangThai' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $dotDotXuat = DotXuatModel::find($id);
        if (!$dotDotXuat) {
            return response()->json([
                'message' => 'Không tìm thấy đợt đột xuất'
            ], 404);
        }
        try {
            if ($request->trangThai == 1) {
                $currentActive = DotXuatModel::where('bTrangThai', '=', 1)->first();
                if ($currentActive) {
                    $currentActive->bTrangThai = 0;
                    $currentActive->save();
                }
            }

            $checkDotTDKT = DotTDKTModel::where('PK_MaDot', '=', $dotDotXuat->FK_MaDot)->first()->bTrangThai;
            if ($checkDotTDKT !== 1) {
                return response()->json([
                    'message' => 'Đợt thi đua khen thưởng chưa được mở'
                ], 404);
            }

            $dotDotXuat->bTrangThai = $request->trangThai;
            $dotDotXuat->save();


            return response()->json([
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $dotDotXuat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật trạng thái đợt đột xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function layThongTinDotDotXuatActive()
    {
        $dotDotXuat = DotXuatModel::where('bTrangThai', '=', 1)->first();
        if ($dotDotXuat) {
            return response()->json([
                'message' => 'Thành công',
                'data' => $dotDotXuat
            ]);
        } else {
            return response()->json([
                'message' => 'Không tìm thấy đợt đột xuất nào đang hoạt động',
            ], 404);
        }
    }
}
