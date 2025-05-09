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

    public function capDanhHieu()
    {
        return $this->belongsTo(CapDanhHieuModel::class, 'FK_MaCap', 'PK_MaCap');
    }
    
    public function loaiDanhHieu()
    {
        return $this->belongsTo(LoaiDanhHieuModel::class, 'FK_MaLoaiDanhHieu', 'PK_MaLoaiDanhHieu');
    }

    public function hinhThuc()
    {
        return $this->belongsTo(HinhThucModel::class, 'FK_MaHinhThuc', 'PK_MaHinhThuc');
    }

    public function deXuat() {
        return $this->hasMany(DeXuatModel::class, 'FK_MaDanhHieu', 'PK_MaDanhHieu');
    }


}