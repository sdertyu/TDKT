<?php

namespace App\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AccountModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'tbltaikhoan';

    protected $hidden = ['password'];

    protected $fillable = ['sUsername', 'sPassword'];

    public function getJWTIdentifier()
    {
        return (string) $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
