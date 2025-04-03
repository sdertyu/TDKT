<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinhChungModel extends Model
{
    use HasFactory;

    protected $table = 'tblminhchung';

    protected $primaryKey = 'PK_MaMinhChung';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'sTenFile',
        'sDuongDan',
        'FK_MaDeXuat'
    ];
}
