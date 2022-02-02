<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements CanResetPassword
{
    use HasFactory, SoftDeletes, HasRoles;

    protected $guard = 'admins';
    protected $guard_name = 'admins';

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

}
