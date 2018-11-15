<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const SEX_UN = 2;
    const SEX_BOY = 1;
    const SEX_GIRL = 0;

    public static $gender = [
        self::SEX_GIRL => '女',
        self::SEX_BOY  => '男',
        self::SEX_UN   => '未知',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sex', 'password', 'avatar',
        'status', 'openid', 'remark', 'phone',
        'integral', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getSexAttribute($value)
    {
        return self::$gender[$value];
    }

}
