<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements CanResetPassword
{
    use HasFactory, SoftDeletes;

    protected $guard = 'admin';

    protected $hidden = [
        'password',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'admin_id', 'id');
    }

}
