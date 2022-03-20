<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements CanResetPassword, JWTSubject
{
    use HasFactory, SoftDeletes, HasRoles;

    protected $guard = 'admins';
    protected $guard_name = 'admins';

    protected $hidden = [
        'password',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

    public function hasPermission($permission)
    {
        return $this->roles->first()->permissions->where('id', $permission)->count();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
