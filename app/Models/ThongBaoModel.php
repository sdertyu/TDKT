<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoModel extends Model
{
    use HasFactory;
    protected $table = 'tblthongbao';

    protected $primaryKey = 'PK_MaThongBao';
    public $incrementing = true;

    protected $keyType = 'int';

    protected $hidden = [''];

    public $timestamps = false;

    protected $casts = [
        'dNgayTao' => 'datetime:d/m/y H:i:s',
    ];

    protected $fillable = ['sTieuDe', 'sNoiDung', 'dNgayTao', 'FK_NguoiTao'];
}
