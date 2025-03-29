<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiDanhHieuModel extends Model
{
    use HasFactory;
    
    protected $table='tblloaidanhhieu';
    protected $primaryKey='PK_MaLoaiDanhHieu';
    public $timestamps = false;
    protected $fillable = ['sTenLoaiDanhhieu'];
}
