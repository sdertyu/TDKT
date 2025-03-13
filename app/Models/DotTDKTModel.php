<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotTDKTModel extends Model
{
    use HasFactory;

    protected $table = 'tbldotthiduakhenthuong';

    protected $primaryKey = 'PK_MaDot';
    public $incrementing = false;
    protected $keyType = 'string';


    public $timestamps = false;
    protected $fillable = ['dNgayTao', 'bTrangThai', 'iNamBatDau','iNamKetThuc'];

    
}
