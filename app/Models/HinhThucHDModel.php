<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhThucHDModel extends Model
{
    use HasFactory;

    protected $table = 'tblhinhthuchd';

    protected $primaryKey = 'PK_HinhThucHD';
    public $incrementing = false;


    public $timestamps = false;
    protected $fillable = ['sTenHinhThuc'];
}
