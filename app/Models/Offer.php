<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory, SoftDeletes;


    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function total()
    {
        return $this->payments()->sum('value');
    }

    public function payments()
    {
        return $this->hasMany(OffersPayment::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Port::class, 'offers_routes', 'offer_id', 'port_id')->withPivot('order');
    }

    public function vessels()
    {
        return $this->belongsToMany(Vessel::class, 'offers_requests_goods_types_vessels', 'offer_id', 'vessel_id');
    }

    public function request_goods_types()
    {
        return $this->belongsToMany(RequestsGoodsType::class, 'offers_requests_goods_types_vessels', 'offer_id', 'request_good_id');
    }

}
