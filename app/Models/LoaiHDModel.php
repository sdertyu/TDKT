<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiHDModel extends Model
{
    use HasFactory;

    protected $table = 'tblloaihoidong';

    protected $primaryKey = 'PK_MaLoaiHD';
    public $incrementing = false;


    public $timestamps = false;
    protected $fillable = ['sTenLoaiHD'];
}
