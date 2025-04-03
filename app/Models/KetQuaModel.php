<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaModel extends Model
{
    use HasFactory;

    protected $table = 'tblketqua';

    protected $primaryKey = 'PK_MaKetQua';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'FK_MaDeXuat',
        'FK_MaHoiDong',
        'iSoNguoiBau',
        'bDuyet',
        'dNgayDuyet'
    ];
}
