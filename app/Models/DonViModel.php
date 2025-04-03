<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonViModel extends Model
{
    use HasFactory;

    protected $table = 'tbldonvi';

    protected $primaryKey = 'PK_MaDonVi';
    public $incrementing = false;
    protected $keyType = 'string';

    // protected $hidden = [''];

    public $timestamps = false;
    protected $fillable = ['sTenDonVi', 'FK_MaTaiKhoan'];

    public function canhan()
    {
        return $this->hasMany(CaNhanModel::class, 'FK_MaDonVi', 'PK_MaDonVi');
    }
}
