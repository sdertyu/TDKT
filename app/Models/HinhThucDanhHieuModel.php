<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhThucDanhHieuModel extends Model
{
    use HasFactory;

    protected $table = "tblhinhthuckhenthuong";
    protected $primaryKey = "PK_MaHinhThucDanhHieu";
    public $timestamps = false;
    protected $fillable = ['sTenHinhThuc'];
}
