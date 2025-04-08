<?php

namespace App\Http\Controllers;

use App\Models\DotTDKTModel;
use App\Models\MinhChungModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MinhChungController extends Controller
{

    public function getListMinhChung($maDeXuat)
    {
        $minhChung = MinhChungModel::whereHas('deXuat', function ($query) use ($maDeXuat) {
            $query->where('PK_MaDeXuat', $maDeXuat);
        })
            ->get()
            ->map(function ($minhChung) {
                return [
                    'PK_MaMinhChung' => $minhChung->PK_MaMinhChung,
                    'sTenFile' => $minhChung->sTenFile,
                    'dNgayTao' => formatDate($minhChung->dNgayTao)
                ];
            });

        if (!$minhChung) {
            return response()->json([
                'error' => 'Không tìm thấy minh chứng'
            ], 404);
        }

        return response()->json([
            'data' => $minhChung
        ], 200);
    }
    public function upLoadMinhChung(Request $request)
    {
        $rules = [
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png',
            'madexuat' => 'required|exists:tbldexuat,PK_MaDeXuat',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $dotActiveModel = DotTDKTModel::where('bTrangThai', 1)->first();
        if (!$dotActiveModel) {
            return response()->json([
                'error' => 'Không có đợt thi đua đang hoạt động.'
            ], 404);
        }

        $dotActive = $dotActiveModel->PK_MaDot;
        $user = auth()->user();
        $uploadedFiles = $request->file('files');

        $filePaths = [];

        foreach ($uploadedFiles as $file) {
            $storedPath = $file->store('minhchung/' . $dotActive . '/' . $user->sUsername . '/' . $request->madexuat);
            $filePaths[] = [
                'path' => $storedPath,
                'name' => $file->getClientOriginalName(),
            ];
        }

        foreach ($filePaths as $file) {
            MinhChungModel::create([
                'sTenFile' => $file['name'],
                'sDuongDan' => $file['path'],
                'FK_MaDeXuat' => $request->madexuat,
                'dNgayTao' => getDateNow(),
            ]);
        }

        return response()->json([
            'message' => 'Lưu minh chứng thành công',
        ], 200);
    }

    public function downloadMinhChung($id)
    {
        $minhChung = MinhChungModel::findOrFail($id);

        // Đường dẫn thực trong storage/app
        $path = $minhChung->sDuongDan;

        if (!Storage::exists($path)) {
            return response()->json(['message' => 'Không tìm thấy file'], 404);
        }

        // Trả file download, giữ tên gốc
        return Storage::download($path, $minhChung->sTenFile);
    }

    public function deleteMinhChung($id)
    {
        $minhChung = MinhChungModel::where('PK_MaMinhChung', '=', $id)->first();
        if (!$minhChung) {
            return response()->json([
                'message' => 'Không tìm thấy văn bản đính kèm'
            ], 404);
        }

        $path = $minhChung->sDuongDan;

        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        $minhChung->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ], 200);
    }

    public function viewMinhChung($id)
    {
        $minhChung = MinhChungModel::findOrFail($id);
        $filePath = storage_path('app/' . $minhChung->sDuongDan);

        abort_unless(file_exists($filePath), 404, 'Không tìm thấy file');

        $mime = mime_content_type($filePath);
        abort_unless($mime === 'application/pdf', 415, 'Chỉ hỗ trợ file PDF');

        return response()->file($filePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $minhChung->sTenFile . '"'
        ]);
    }
}
