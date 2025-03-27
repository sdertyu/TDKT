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
}
