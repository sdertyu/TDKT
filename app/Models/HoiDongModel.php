<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoiDongModel extends Model
{
    use HasFactory;

    protected $table = 'tblhoidong';

    protected $primaryKey = 'PK_MaHoiDong';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
    protected $fillable = ['FK_MaTaiKhoan', 'FK_MaDot', 'dNgayTao', 'FK_ChuTri', 'FK_ThuKy', 'dThoiGianHop', 'sDiaChi', 'iSoNguoiThamDu', 'iSoThanhVien', 'FK_ChiTietHD', 'sDuongDan', 'sTenFile', 'sSoHD', 'sGhiChu', 'FK_MaLoaiHD', 'FK_MaHinhThuc', 'FK_MaDotXuat'];
}
