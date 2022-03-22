<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'requests_responses';

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function total()
    {
        return $this->payments()->sum('value');
    }

    public function payments()
    {
        return $this->hasMany(RequestResponsePayment::class, 'offer_id');
    }

    public function routes()
    {
        return $this->belongsToMany(Port::class, 'requests_responses_routes', 'offer_id', 'port_id')->withPivot('order');
    }

    public function vessels()
    {
        return $this->belongsToMany(Vessel::class, 'requests_responses_requests_goods_types_vessels', 'offer_id', 'vessel_id')->withPivot('request_good_id');
    }

    public function request_goods_types()
    {
        return $this->belongsToMany(RequestsGoodsType::class, 'requests_responses_requests_goods_types_vessels', 'offer_id', 'request_good_id')->withPivot('vessel_id');
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

    public function origin()
    {
        return $this->morphOne(Contract::class, 'origin');
    }

}
