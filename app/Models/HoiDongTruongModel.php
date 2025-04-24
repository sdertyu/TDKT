<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoiDongTruongModel extends Model
{
    use HasFactory;

    protected $table = 'tblhoidongtruong';

    protected $primaryKey = 'PK_MaHoiDong';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
    protected $fillable = ['FK_MaTaiKhoan', 'FK_MaDot', 'dNgayTao', 'dThoiGianHop', 'sDiaChi', 'iSoNguoiThamDu', 'iSoThanhVien', 'FK_ChiTietHD', 'sDuongDan', 'sTenFile', 'sSoHD', 'sGhiChu', 'FK_MaLoaiHD', 'FK_MaHinhThuc', 'FK_MaDotXuat'];

    public function deXuat()
    {
        return $this->hasMany(DeXuatModel::class, 'FK_MaHoiDong', 'PK_MaHoiDong');
    }

    public function dot()
    {
        return $this->belongsTo(DotTDKTModel::class, 'FK_MaDot', 'PK_MaDot');
    }

    public function dotXuat() {
        return $this->belongsTo(DotTDKTModel::class, 'FK_MaDotXuat', 'PK_MaDotXuat');
    }
}
