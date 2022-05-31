<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory, SoftDeletes;


    public function responses()
    {
        return $this->hasMany(OfferResponse::class);
    }

    public function port_from()
    {
        return $this->belongsTo(Port::class, 'port_from');
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }
}
