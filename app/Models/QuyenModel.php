<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenModel extends Model
{
    use HasFactory;

    protected $table = 'tbltaikhoan';

    protected $primaryKey = 'PK_MaQuyen';
    public $incrementing = false;
    protected $keyType = 'string';

    // protected $hidden = [''];

    public $timestamps = false;
    protected $fillable = ['sTenQuyen'];

    public function account()
    {
        return $this->hasMany(AccountModel::class);
    }
}
