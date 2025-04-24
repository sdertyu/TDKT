<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeXuatModel extends Model
{
    use HasFactory;

    protected $table = 'tbldexuat';

    protected $primaryKey = 'PK_DeXuat';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $hidden = [''];

    public $timestamps = false;
    protected $casts = [
        'dNgayTao' => 'datetime:H:i:s d/m/y',
    ];

    protected $fillable = ['FK_User', 'FK_MaHoiDong', 'iSoNguoiBau', 'dNgayTao', 'FK_MaHoiDong', 'FK_MaDanhHieu', 'FK_MaDotXuat', 'FK_NguoiTao'];

    public function hoiDongDonVi() {
        return $this->belongsTo(HoiDongDonViModel::class, 'FK_MaHoiDong', 'PK_MaHoiDong');
    }

    public function danhHieu() {
        return $this->belongsTo(DanhHieuModel::class, 'FK_MaDanhHieu', 'PK_MaDanhHieu');
    }

    public function taiKhoan() {
        return $this->belongsTo(AccountModel::class, 'FK_User', 'PK_MaTaiKhoan');
    }
    
    public function ketQua() {
        return $this->hasOne(KetQuaModel::class, 'FK_MaDeXuat', 'PK_MaDeXuat');
    }

    public function dotXuat() {
        return $this->belongsTo(DotXuatModel::class, 'FK_MaDotXuat', 'PK_MaDotXuat');
    }
}
