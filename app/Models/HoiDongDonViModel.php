<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoiDongDonViModel extends Model
{
    use HasFactory;

    protected $table = 'tblhoidongdonvi';

    protected $primaryKey = 'PK_MaHoiDong';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
    protected $fillable = ['FK_MaTaiKhoan', 'FK_MaDot', 'dNgayTao', 'FK_ChuTri', 'FK_ThuKy', 'dThoiGianHop', 'sDiaChi', 'iSoNguoiThamDu', 'iSoThanhVien', 'sDuongDan', 'sTenFile', 'sSoHD', 'sGhiChu', 'FK_MaLoaiHD', 'FK_MaHinhThuc'];

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
