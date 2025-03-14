<?php

namespace App\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AccountModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'tbltaikhoan';

    protected $primaryKey = 'PK_MaTaiKhoan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = ['sPassword'];

    public $timestamps = false;
    protected $fillable = ['sUsername', 'sPassword', 'api_token'];

    public function donVi()
    {
        return $this->hasMany(DonViModel::class, 'FK_MaTaiKhoan', 'PK_MaTaiKhoan');
    }

    public function caNhan()
    {
        return $this->hasMany(CaNhanModel::class, 'FK_MaTaiKhoan', 'PK_MaTaiKhoan');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsTo(QuyenModel::class);
    }
}
