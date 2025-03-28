<?php

namespace App\Http\Controllers;

use App\Models\DotTDKTModel;
use App\Models\VBDKModel;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'dNgayTao' => $carbon->format('Y-m-d H:i:s')
        ]);

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
            'file.required' => 'Bạn chưa chọn file',
            'file.file' => 'Vui lòng nhập đúng file',
            'file.mimes' => 'Định dạng file không hợp lệ.',
            'tenvanban.required' => 'Vui lòng nhập tên văn bản'
        ];

        $validator = Validator::make($request->all(), [
            'madot' => 'required|exists:tbldotthiduakhenthuong,PK_MaDot',
            'tenvanban' => 'required',
            'file' => 'required',
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
            $filePath = $file->move('vanbandinhkem/' . $folderDot, $file->getClientOriginalName());

            $filePaths[] = [
                'path' => 'vanbandinhkem/' . $folderDot . '/' . $file->getClientOriginalName(),
                'name' => pathinfo($filePath)['filename']
            ];
        }

        foreach ($filePaths as $file) {
            $vanBanDinhKem = new VBDKModel();
            $vanBanDinhKem->FK_MaDot = $request->madot;
            $vanBanDinhKem->sTenVanBan = $request->tenvanban;
            $vanBanDinhKem->sTenFile = $file['name'];
            $vanBanDinhKem->dNgayTao = now();
            $vanBanDinhKem->sDuongDan = $file['path'];
            $vanBanDinhKem->save();
        }

        return response()->json([
            'message' => 'Thêm thành công',
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
}
