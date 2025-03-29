<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDHModel extends Model
{
    use HasFactory;

    protected $table = 'tblchitietdanhhieu';

    protected $primaryKey = 'PK_MaChiTiet';

    // protected $hidden = [''];

    public $timestamps = false;
    protected $fillable = ['FK_MaHinhThuc', 'FK_MaLoaiDanhHieu'];

    // public function hinhThuc()
    // {
    //     return $this->belongsTo(HinhThucDanhHieuModel::class, 'FK_MaHinhThuc', 'PK_MaHinhThuc');
    // }

    // public function danhHieu()
    // {
    //     return $this->hasOne(DanhHieuModel::class, 'FK_MaChiTiet', 'PK_MaDanhHieu');
    // }

    // public function loaiDanhHieu()
    // {
    //     return $this->belongsTo(LoaiDanhHieuModel::class, 'FK_MaLoaiDanhHieu', 'PK_MaLoaiDanhHieu');
    // }
}
