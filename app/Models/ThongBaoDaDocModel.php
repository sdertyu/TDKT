<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoDaDocModel extends Model
{
    use HasFactory;

    protected $table = 'tblthongbao_dadoc';

    protected $primaryKey = null;

    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'FK_MaThongBao',
        'FK_MaTaiKhoan',
    ];
}
