<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapDanhHieuModel extends Model
{
    use HasFactory;

    protected $table = 'tblcapdanhhieu';

    protected $primaryKey = 'PK_MaCap';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'sTenCap',
    ];
}
