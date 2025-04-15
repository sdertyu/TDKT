<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoModel extends Model
{
    use HasFactory;
    protected $table = 'tblthongbao';

    protected $primaryKey = 'PK_MaThongBao';
    public $incrementing = true;

    protected $keyType = 'int';

    protected $hidden = [''];

    public $timestamps = false;

    protected $casts = [
        'dNgayTao' => 'datetime:H:i:s d/m/y',
    ];

    protected $fillable = ['sTieuDe', 'sNoiDung', 'dNgayTao', 'FK_NguoiTao'];

    public function thongBaoQuyen()
    {
        return $this->hasMany(ThongBaoQuyenModel::class, 'FK_MaThongBao', 'PK_MaThongBao');
    }

    public function thongBaoTaiKhoan() {
        return $this->hasMany(ThongBaoTaiKhoanModel::class, 'FK_MaThongBao', 'PK_MaThongBao');
    }
}
