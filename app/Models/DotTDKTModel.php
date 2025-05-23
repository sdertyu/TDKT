<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DotTDKTModel extends Model
{
    use HasFactory;

    protected $table = 'tbldotthiduakhenthuong';

    protected $primaryKey = 'PK_MaDot';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'dNgayTao' => 'datetime:H:i:s d-m-Y'
    ];


    public $timestamps = false;
    protected $fillable = ['PK_MaDot', 'dNgayTao', 'bTrangThai', 'iNamBatDau', 'iNamKetThuc', 'dHanBienBanDonVi', 'dHanNopMinhChung', 'dHanBienBanHoiDong'];

    public function vanbandinhkem()
    {
        return $this->hasMany(VBDKModel::class, 'FK_MaDot', 'PK_MaDot');
    }

    public function hoiDong() {
        return $this->hasMany(HoiDongModel::class, 'FK_MaDot', 'PK_MaDot');
    }

}
