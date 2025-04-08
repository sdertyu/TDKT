<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhThucModel extends Model
{
    use HasFactory;

    protected $table = "tblhinhthuc";
    protected $primaryKey = "PK_MaHinhThuc";
    public $timestamps = false;
    protected $fillable = ['sTenHinhThuc'];
}
