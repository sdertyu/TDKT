<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoTaiKhoanModel extends Model
{
    use HasFactory;

    protected $table = 'tblthongbao_taikhoan';

    protected $primaryKey = null;

    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'FK_MaThongBao',
        'FK_MaTaiKhoan',
        'bTrangThai'
    ];

    public function taiKhoan() {
        return $this->belongsTo(AccountModel::class, 'FK_MaTaiKhoan', 'PK_MaTaiKhoan');
    }
}
