<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotXuatModel extends Model
{
    use HasFactory;
    protected $table = 'tbldotxuat';

    protected $primaryKey = 'PK_MaDotXuat';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'PK_MaDotXuat',
        'dNgayBatDau',
        'dNgayKetThuc',
        'FK_MaDot',
        'dHanNopMinhChung',
        'dHanNopDeXuat',
        'dHanBienBanHoiDong',
        'bTrangThai',
        'FK_NguoiTao'
    ];

    // protected $casts = [
    //     'dNgayBatDau' => 'date:d/m/Y',
    //     'dNgayKetThuc' => 'date:d/m/Y',
    //     'dHanNopMinhChung' => 'date:d/m/Y',
    //     'dHanBienBanHoiDong' => 'date:d/m/Y',
    // ];
}
