<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhiemVuModel extends Model {
    use HasFactory;

    protected $table = 'tblnhiemvuhoidong';

    protected $primaryKey = 'PK_MaNhiemVu';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'sTenNhiemVu',
    ];
}