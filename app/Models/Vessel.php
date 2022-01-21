<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vessel extends Model
{
    use HasFactory, SoftDeletes;


    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function type()
    {
        return $this->belongsTo(vType::class, 'type_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function crew()
    {
        return $this->hasMany(Crew::class);
    }

//    public function type()
//    {
//        return $this->belongsTo(User::class);
//    }
}
