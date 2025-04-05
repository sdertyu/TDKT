<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhHieuModel extends Model
{
    use HasFactory;

    protected $table = 'tbldanhhieu';

    protected $primaryKey = 'PK_MaDanhHieu';

    // protected $hidden = [''];

    public $timestamps = false;
    protected $fillable = ['FK_MaHinhThuc', 'FK_MaLoaiDanhHieu', 'FK_MaCap', 'sTenDanhHieu', 'bTrangThai'];

    // public function chiTietDanhHieus()
    // {
    //     return $this->belongsTo(ChiTietDHModel::class, 'FK_MaChiTiet', 'PK_MaChiTiet');
    // }
}
