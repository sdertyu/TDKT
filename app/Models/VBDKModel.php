<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VBDKModel extends Model
{
    use HasFactory;

    protected $table = 'tblvanbandinhkem';

    protected $primaryKey = 'PK_MaVanBan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'dNgayTao' => 'datetime:H:i:s d-m-Y'
    ];


    public $timestamps = false;
    protected $fillable = ['sTenVanBan', 'FK_MaDot', 'sTenFile', 'dNgayTao', 'sDuongDan'];

    public function dottdkt()
    {
        return $this->belongsTo(DotTDKTModel::class, 'FK_MaDot', 'PK_MaDot');
    }
}
