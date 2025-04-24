<?php

namespace App\Models;

use App\Http\Controllers\TaiKhoanController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVienHoiDongModel extends Model
{
    use HasFactory;

    protected $table = 'tblthanhvienhoidong';

    protected $primaryKey = 'PK_MaThanhVienHoiDong';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'FK_TaiKhoan',
        'FK_MaKienToan',
        'FK_MaNhiemVu',
    ];

    public function taiKhoan()
    {
        return $this->belongsTo(AccountModel::class, 'FK_TaiKhoan', 'PK_MaTaiKhoan');
    }

    public function nhiemVu()
    {
        return $this->belongsTo(NhiemVuModel::class, 'FK_MaNhiemVu', 'PK_MaNhiemVu');
    }
}