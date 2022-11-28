<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class State extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . App::getLocale()};
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'state_id', 'id');
    }
}
