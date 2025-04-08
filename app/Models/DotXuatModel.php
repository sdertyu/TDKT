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
        'dNgayBatDau',
        'dNgayKetThuc',
        'FK_MaDot',
        'dHanNopMinhChung',
        'dHanBienBanHoiDong',
    ];
}
