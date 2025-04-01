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
        'dNgayTao' => 'datetime:d/m/y H:i:s',
    ];

    protected $fillable = ['FK_User', 'FK_MaHoiDong', 'iSoNguoiBau', 'sLink', 'dNgayTao'];

}
