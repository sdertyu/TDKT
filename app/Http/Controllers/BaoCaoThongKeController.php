<?php

namespace App\Http\Controllers;

use App\Models\CaNhanModel;
use App\Models\CapDanhHieuModel;
use App\Models\ChiTietDHModel;
use App\Models\ChiTietHDModel;
use App\Models\DanhHieuModel;
use App\Models\DeXuatModel;
use App\Models\DonViModel;
use App\Models\DotTDKTModel;
use App\Models\HinhThucModel;
use App\Models\KetQuaModel;
use App\Models\LoaiDanhHieuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BaoCaoThongKeController extends Controller
{
    public function thongKeThanhTichCuaToi()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $thanhTich = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                ])
                ->where('FK_User', $id)
                ->get()
                ->map(function ($item) {
                    return [
                        'tenDanhHieu' => $item->danhHieu->sTenDanhHieu,
                        'dot' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                    ];
                });

            return response()->json([
                'data' => $thanhTich
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách thành tích của tôi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách thành tích của tôi'
            ], 500);
        }
    }

    public function thongKeThanhTichCuaCaNhanTrongDonVi()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $donVi = DonViModel::where('FK_MaTaiKhoan', $id)->first();
            if (!$donVi) {
                return response()->json([
                    'message' => 'Đơn vị không tồn tại'
                ], 404);
            }
            $maDonVi = $donVi->PK_MaDonVi;
            $thanhTich = DeXuatModel::whereHas('ketQua', function ($query) {
                $query->where('bDuyet', 1);
            })
                ->whereHas('taiKhoan.caNhan', function ($query) use ($maDonVi) {
                    $query->where('FK_MaDonVi', $maDonVi);
                })
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'taiKhoan.caNhan',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'maCaNhan' => $item->taiKhoan->caNhan->PK_MaCaNhan ?? null,
                        'tenCaNhan' => $item->taiKhoan->caNhan->sTenCaNhan ?? null,
                        'tenDanhHieu' => $item->danhHieu->sTenDanhHieu,
                        'dot' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                    ];
                });

            return response()->json([
                'data' => $thanhTich
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách thành tích của cá nhân trong đơn vị: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách thành tích của cá nhân trong đơn vị'
            ], 500);
        }
    }

    public function danhSachNamHoc()
    {
        try {
            $namHoc = DotTDKTModel::select('PK_MaDot as namHoc')
                ->orderBy('PK_MaDot', 'desc')
                ->get();
            return response()->json([
                'data' => $namHoc
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách năm học: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách năm học'
            ], 500);
        }
    }

    public function danhSachDanhHieu()
    {
        try {
            $danhHieu = DanhHieuModel::select('PK_MaDanhHieu as maDanhHieu', 'sTenDanhHieu as tenDanhHieu')
                ->orderBy('sTenDanhHieu', 'asc')
                ->get();

            return response()->json([
                'data' => $danhHieu
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách danh hiệu'
            ], 500);
        }
    }

    public function danhSachCapDanhHieu()
    {
        try {
            $capDanhHieu = CapDanhHieuModel::select('PK_MaCap as maCap', 'sTenCap as tenCap')
                ->orderBy('sTenCap', 'asc')
                ->get();

            return response()->json([
                'data' => $capDanhHieu
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách cấp danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách cấp danh hiệu'
            ], 500);
        }
    }

    public function dataThongKeDanhHieu()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $data = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'hoiDongDonVi',
                    'taiKhoan.caNhan',
                    'taiKhoan.donVi',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'ten' => $item->taiKhoan->caNhan == null ? $item->taiKhoan->donVi->sTenDonVi : $item->taiKhoan->caNhan->sTenCaNhan,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'loai' => $item->danhHieu->loaiDanhHieu->sTenLoaiDanhHieu,
                        'doiTuong' => $item->taiKhoan->caNhan == null ? 'Đơn vị' : 'Cá nhân',
                    ];
                });

            return response()->json([
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy dữ liệu thống kê danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê danh hiệu'
            ], 500);
        }
    }

    public function dataThongKeCaNhan()
    {
        try {
            $user = auth()->user();
            $id = $user->PK_MaTaiKhoan;
            $data = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->whereHas('taiKhoan.caNhan.donVi',)
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                    'hoiDongDonVi',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'hoTen' => $item->taiKhoan->caNhan->sTenCaNhan,
                        'donVi' => $item->taiKhoan->caNhan->donVi->sTenDonVi,
                        'maDanhHieu' => $item->danhHieu->PK_MaDanhHieu,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'loai' => $item->danhHieu->loaiDanhHieu->sTenLoaiDanhHieu,
                    ];
                });

            return response()->json([
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy dữ liệu thống kê danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê danh hiệu'
            ], 500);
        }
    }

    public function dataThongKeDonVi()
    {
        try {

            $data = DeXuatModel::whereHas('ketQua', function ($query) {
                return $query->where('bDuyet', 1);
            })
                ->whereHas('taiKhoan.donVi.caNhan')
                ->with([
                    'danhHieu',
                    'danhHieu.capDanhHieu',
                    'danhHieu.loaiDanhHieu',
                    'danhHieu.hinhThuc',
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'tenDonVi' => $item->taiKhoan->donVi->sTenDonVi,
                        'danhHieu' => $item->danhHieu->sTenDanhHieu,
                        'namHoc' => $item->FK_MaDot,
                        'hinhThuc' => $item->danhHieu->hinhThuc->sTenHinhThuc,
                        'capDanhHieu' => $item->danhHieu->capDanhHieu->sTenCap,
                        'soLuongDat' => 1,
                    ];
                });

            // $group = [];
            // foreach ($data as $item) {
            //     if (!isset($group[$item['tenDonVi']])) {
            //         $group[$item['tenDonVi']] = 0;
            //     }
            //     $group[$item['tenDonVi']]++;
            // }

            // $data = $data->transform(function ($item) use ($group) {
            //     $item['soLuongDat'] = $group[$item['tenDonVi']];
            //     return $item;
            // });


            return response()->json([
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy dữ liệu thống kê danh hiệu: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu thống kê danh hiệu'
            ], 500);
        }
    }

    public function danhSachDonVi()
    {
        try {
            $donVi = DonViModel::select('PK_MaDonVi as maDonVi', 'sTenDonVi as tenDonVi')
                ->select('PK_MaDonVi as maDonVi', 'sTenDonVi as tenDonVi')
                ->orderBy('sTenDonVi', 'asc')
                ->get();
            return response()->json([
                'data' => $donVi
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách đơn vị: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách đơn vị'
            ], 500);
        }
    }

    public function danhSachHinhThuc()
    {
        try {
            $hinhThuc = HinhThucModel::select('PK_MaHinhThuc as maHinhThuc', 'sTenHinhThuc as tenHinhThuc')
                ->orderBy('sTenHinhThuc', 'asc')
                ->get();
            return response()->json([
                'data' => $hinhThuc
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách đơn vị: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách đơn vị'
            ], 500);
        }
    }

    public function exportExcelCaNhan(Request $request)
    {
        // Get data from request
        $data = $request->input('data');
        $yearlyChartImage = $request->input('yearlyChartImage');
        $individualChartImage = $request->input('individualChartImage');
        $awardChartImage = $request->input('awardChartImage');
        $unitChartImage = $request->input('unitChartImage');

        // Process base64 images
        $yearlyChartImageData = null;
        $individualChartImageData = null;
        $awardChartImageData = null;
        $unitChartImageData = null;

        if ($yearlyChartImage) {
            // Remove the data:image/png;base64, prefix
            $yearlyChartImage = substr($yearlyChartImage, strpos($yearlyChartImage, ',') + 1);
            $yearlyChartImageData = base64_decode($yearlyChartImage);

            // Save temporarily
            $yearlyChartPath = storage_path('app/public/images/yearly_chart_' . time() . '.png');
            file_put_contents($yearlyChartPath, $yearlyChartImageData);
        }

        if ($individualChartImage) {
            // Remove the data:image/png;base64, prefix
            $individualChartImage = substr($individualChartImage, strpos($individualChartImage, ',') + 1);
            $individualChartImageData = base64_decode($individualChartImage);

            // Save temporarily
            $individualChartPath = storage_path('app/public/images/individual_chart_' . time() . '.png');
            file_put_contents($individualChartPath, $individualChartImageData);
        }

        if ($awardChartImage) {
            // Remove the data:image/png;base64, prefix
            $awardChartImage = substr($awardChartImage, strpos($awardChartImage, ',') + 1);
            $awardChartImageData = base64_decode($awardChartImage);

            // Save temporarily
            $awardChartPath = storage_path('app/public/images/award_chart_' . time() . '.png');
            file_put_contents($awardChartPath, $awardChartImageData);
        }

        if ($unitChartImage) {
            // Remove the data:image/png;base64, prefix
            $unitChartImage = substr($unitChartImage, strpos($unitChartImage, ',') + 1);
            $unitChartImageData = base64_decode($unitChartImage);

            // Save temporarily
            $unitChartPath = storage_path('app/public/images/unit_chart_' . time() . '.png');
            file_put_contents($unitChartPath, $unitChartImageData);
        }



        // Create Excel spreadsheet with PHPSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ CÁ NHÂN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add charts if available
        $currentRow = 3;

        if (isset($yearlyChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Yearly Chart');
            $drawing->setDescription('Thống kê danh hiệu theo năm học');
            $drawing->setPath($yearlyChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(600);

            $currentRow += 20; // Add space for the chart
        }

        if (isset($individualChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Individual Chart');
            $drawing->setDescription('Thống kê danh hiệu theo cá nhân');
            $drawing->setPath($individualChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(400);

            $currentRow += 20; // Add space for the chart
        }
        if (isset($awardChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Award Chart');
            $drawing->setDescription('Thống kê danh hiệu theo danh hiệu');
            $drawing->setPath($awardChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(400);

            $currentRow += 20; // Add space for the chart
        }
        if (isset($unitChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Unit Chart');
            $drawing->setDescription('Thống kê danh hiệu theo đơn vị');
            $drawing->setPath($unitChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(400);

            $currentRow += 20; // Add space for the chart
        }

        // Add table headers
        $currentRow += 2;
        $headers = ['STT', 'Tên cá nhân/tập thể', 'Danh hiệu', 'Năm học', 'Hình thức', 'Cấp danh hiệu'];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // Convert to Excel column (A, B, C...)
            $sheet->setCellValue($column . $currentRow, $header);
            $sheet->getStyle($column . $currentRow)->getFont()->setBold(true);
        }

        // Style the header row
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');

        // Add data rows
        $rowIndex = $currentRow + 1;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $rowIndex, $index + 1);
            $sheet->setCellValue('B' . $rowIndex, $item['hoTen']);
            $sheet->setCellValue('C' . $rowIndex, $item['danhHieu']);
            $sheet->setCellValue('D' . $rowIndex, $item['namHoc']);
            $sheet->setCellValue('E' . $rowIndex, $item['hinhThuc']);
            $sheet->setCellValue('F' . $rowIndex, $item['capDanhHieu']);
            $rowIndex++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create borders for data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $currentRow . ':F' . ($rowIndex - 1))->applyFromArray($styleArray);

        // Save Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $excelPath = storage_path('app/public/thongke_danhhieu_' . time() . '.xlsx');
        $writer->save($excelPath);

        // Clean up temporary image files
        if (isset($yearlyChartPath)) {
            unlink($yearlyChartPath);
        }
        if (isset($levelChartPath)) {
            unlink($levelChartPath);
        }

        // Return Excel file for download
        return response()->download($excelPath, 'thongke_danhhieu.xlsx')->deleteFileAfterSend(true);
    }
    public function exportExcelDonVi(Request $request)
    {
        // Get data from request
        $data = $request->input('data');
        Log::info($data);
        $unitChartImage = $request->input('unitChartImage');
        $typeChartImage = $request->input('typeChartImage');

        // Process base64 images
        $unitChartImageData = null;
        $typeChartImageData = null;

        if ($unitChartImage) {
            // Remove the data:image/png;base64, prefix
            $unitChartImage = substr($unitChartImage, strpos($unitChartImage, ',') + 1);
            $unitChartImageData = base64_decode($unitChartImage);

            // Save temporarily
            $unitChartPath = storage_path('app/public/images/yearly_chart_' . time() . '.png');
            file_put_contents($unitChartPath, $unitChartImageData);
        }

        if ($typeChartImage) {
            // Remove the data:image/png;base64, prefix
            $typeChartImage = substr($typeChartImage, strpos($typeChartImage, ',') + 1);
            $typeChartImageData = base64_decode($typeChartImage);

            // Save temporarily
            $typeChartPath = storage_path('app/public/images/individual_chart_' . time() . '.png');
            file_put_contents($typeChartPath, $typeChartImageData);
        }



        // Create Excel spreadsheet with PHPSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ CÁ NHÂN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add charts if available
        $currentRow = 3;

        if (isset($unitChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Unit Chart');
            $drawing->setDescription('Thống kê danh hiệu theo đơn vị');
            $drawing->setPath($unitChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(600);

            $currentRow += 20; // Add space for the chart
        }

        if (isset($typeChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Type Chart');
            $drawing->setDescription('Thống kê danh hiệu theo loại danh hiệu');
            $drawing->setPath($typeChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(400);

            $currentRow += 20; // Add space for the chart
        }

        // Add table headers
        $currentRow += 2;
        $headers = ['STT', 'Tên cá nhân/tập thể', 'Danh hiệu', 'Năm học', 'Hình thức', 'Cấp danh hiệu'];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // Convert to Excel column (A, B, C...)
            $sheet->setCellValue($column . $currentRow, $header);
            $sheet->getStyle($column . $currentRow)->getFont()->setBold(true);
        }

        // Style the header row
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');

        // Add data rows
        $rowIndex = $currentRow + 1;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $rowIndex, $index + 1);
            $sheet->setCellValue('B' . $rowIndex, $item['tenDonVi']);
            $sheet->setCellValue('C' . $rowIndex, $item['danhHieu']);
            $sheet->setCellValue('D' . $rowIndex, $item['namHoc']);
            $sheet->setCellValue('E' . $rowIndex, $item['hinhThuc']);
            $sheet->setCellValue('F' . $rowIndex, $item['capDanhHieu']);
            $rowIndex++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create borders for data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $currentRow . ':F' . ($rowIndex - 1))->applyFromArray($styleArray);

        // Save Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $excelPath = storage_path('app/public/thongke_danhhieu_' . time() . '.xlsx');
        $writer->save($excelPath);

        // Clean up temporary image files
        if (isset($yearlyChartPath)) {
            unlink($yearlyChartPath);
        }
        if (isset($levelChartPath)) {
            unlink($levelChartPath);
        }

        // Return Excel file for download
        return response()->download($excelPath, 'thongke_danhhieu.xlsx')->deleteFileAfterSend(true);
    }


    public function exportExcelDanhHieu(Request $request)
    {
        // Get data from request
        $data = $request->input('data');
        $yearlyChartImage = $request->input('yearlyChartImage');
        $levelChartImage = $request->input('levelChartImage');

        // Process base64 images
        $yearlyChartImageData = null;
        $levelChartImageData = null;

        if ($yearlyChartImage) {
            // Remove the data:image/png;base64, prefix
            $yearlyChartImage = substr($yearlyChartImage, strpos($yearlyChartImage, ',') + 1);
            $yearlyChartImageData = base64_decode($yearlyChartImage);

            // Save temporarily
            $yearlyChartPath = storage_path('app/public/images/yearly_chart_' . time() . '.png');
            file_put_contents($yearlyChartPath, $yearlyChartImageData);
        }

        if ($levelChartImage) {
            // Remove the data:image/png;base64, prefix
            $levelChartImage = substr($levelChartImage, strpos($levelChartImage, ',') + 1);
            $levelChartImageData = base64_decode($levelChartImage);

            // Save temporarily
            $levelChartPath = storage_path('app/public/images/level_chart_' . time() . '.png');
            file_put_contents($levelChartPath, $levelChartImageData);
        }

        // Create Excel spreadsheet with PHPSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ DANH HIỆU');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add charts if available
        $currentRow = 3;

        if (isset($yearlyChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Yearly Chart');
            $drawing->setDescription('Thống kê danh hiệu theo năm học');
            $drawing->setPath($yearlyChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(600);

            $currentRow += 20; // Add space for the chart
        }

        if (isset($levelChartPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Level Chart');
            $drawing->setDescription('Tỷ lệ theo cấp danh hiệu');
            $drawing->setPath($levelChartPath);
            $drawing->setCoordinates('A' . $currentRow);
            $drawing->setWorksheet($sheet);
            $drawing->setWidth(400);

            $currentRow += 20; // Add space for the chart
        }

        // Add table headers
        $currentRow += 2;
        $headers = ['STT', 'Tên cá nhân/tập thể', 'Danh hiệu', 'Năm học', 'Hình thức', 'Cấp danh hiệu'];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // Convert to Excel column (A, B, C...)
            $sheet->setCellValue($column . $currentRow, $header);
            $sheet->getStyle($column . $currentRow)->getFont()->setBold(true);
        }

        // Style the header row
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');

        // Add data rows
        $rowIndex = $currentRow + 1;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $rowIndex, $index + 1);
            $sheet->setCellValue('B' . $rowIndex, $item['ten']);
            $sheet->setCellValue('C' . $rowIndex, $item['danhHieu']);
            $sheet->setCellValue('D' . $rowIndex, $item['namHoc']);
            $sheet->setCellValue('E' . $rowIndex, $item['hinhThuc']);
            $sheet->setCellValue('F' . $rowIndex, $item['capDanhHieu']);
            $rowIndex++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create borders for data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $currentRow . ':F' . ($rowIndex - 1))->applyFromArray($styleArray);

        // Save Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $excelPath = storage_path('app/public/thongke_danhhieu_' . time() . '.xlsx');
        $writer->save($excelPath);

        // Clean up temporary image files
        if (isset($yearlyChartPath)) {
            unlink($yearlyChartPath);
        }
        if (isset($levelChartPath)) {
            unlink($levelChartPath);
        }

        // Return Excel file for download
        return response()->download($excelPath, 'thongke_danhhieu.xlsx')->deleteFileAfterSend(true);
    }

    public function layDanhSachCaNhanTheoDonVi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donVi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        $donVi = $request->input('donVi');

        try {
            $caNhan = CaNhanModel::whereIn('FK_MaDonVi', $donVi)
                ->select('PK_MaCaNhan as maCaNhan', 'sTenCaNhan as tenCaNhan')
                ->orderBy('sTenCaNhan', 'asc')
                ->get();

            return response()->json([
                'data' => $caNhan,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách cá nhân theo đơn vị: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách cá nhân theo đơn vị'
            ], 500);
        }
    }
}
