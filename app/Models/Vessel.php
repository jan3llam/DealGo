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

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function request_responses()
    {
        return $this->belongsToMany(RequestResponse::class, 'requests_responses_requests_goods_types_vessels', 'vessel_id', 'offer_id');
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }
}
