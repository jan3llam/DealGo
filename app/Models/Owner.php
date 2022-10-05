<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use HasFactory, SoftDeletes;

    public function vessels()
    {
        return $this->hasMany(Vessel::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
