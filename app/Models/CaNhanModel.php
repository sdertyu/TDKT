<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaNhanModel extends Model
{
    use HasFactory;

    protected $table = 'tblcanhan';

    protected $primaryKey = 'PK_MaCaNhan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [''];

    public $timestamps = false;
    protected $fillable = ['sTenCaNhan', 'sEmail', 'sTenChucVu', 'bGioiTinh'];

    public function donvi()
    {
        return $this->belongsTo(DonViModel::class, 'FK_MaDonVi', 'PK_MaDonVi');
    }

    public function taikhoan()
    {
        return $this->belongsTo(AccountModel::class, 'FK_MaTaiKhoan', 'PK_MaTaiKhoan');
    }
}
