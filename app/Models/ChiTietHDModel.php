<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietHDModel extends Model
{
    use HasFactory;

    protected $table = 'tblchitiethd';
    protected $primaryKey = 'PK_MaChiTietHD';

    public $timestamps = false;
    protected $fillable = ['FK_MaLoaiHD','FK_HinhThucHD'];
}
