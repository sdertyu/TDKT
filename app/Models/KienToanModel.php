<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KienToanModel extends Model
{
    use HasFactory;

    protected $table = 'tblkientoan';

    protected $primaryKey = 'PK_MaKienToan';

     // Add this line to fix the issue:
     public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'PK_MaKienToan',
        'dNgayTao',
        'sTenFile',
        'sDuongDan',
        'bTrangThai',
    ];

    public function thanhVienHoiDong()
    {
        return $this->hasMany(ThanhVienHoiDongModel::class, 'FK_MaKienToan', 'PK_MaKienToan');
    }

    public function hoiDongTruong() {
        return $this->hasMany(HoiDongTruongModel::class, 'FK_MaKienToan', 'PK_MaKienToan');
    }
}