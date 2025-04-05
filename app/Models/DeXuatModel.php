<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeXuatModel extends Model
{
    use HasFactory;

    protected $table = 'tbldexuat';

    protected $primaryKey = 'PK_DeXuat';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $hidden = [''];

    public $timestamps = false;
    protected $casts = [
        'dNgayTao' => 'datetime:H:i:s d/m/y',
    ];

    protected $fillable = ['FK_User', 'FK_MaHoiDong', 'iSoNguoiBau', 'sLink', 'dNgayTao', 'FK_MaHoiDong'];

}
