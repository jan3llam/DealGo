<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'requests_responses';
    protected $appends = ['approved', 'matrix'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function parent()
    {
        return $this->request();
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Port::class, 'requests_responses_routes', 'offer_id', 'port_id')->withPivot('order');
    }

    public function request_goods_types()
    {
        return $this->belongsToMany(RequestsGoodsType::class, 'requests_responses_requests_goods_types_vessels', 'offer_id', 'request_good_id')->withPivot(['vessel_id', 'weight']);
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

    public function origin()
    {
        return $this->morphOne(Contract::class, 'origin');
    }

    public function getApprovedAttribute()
    {
        return $this->origin ? 1 : 0;
    }

    public function getMatrixAttribute()
    {
        // 1 price
        // 2 vessel age
        // 3 maintenance number
        // 4 shipments number
        // 5 rate
        // 6 nearest date
        return [
            1 => intval($this->total()),
            2 => $this->vessels()->get()->pluck('build_year', 'id')->toArray(),
            3 => $this->vessels()->withCount('maintenance')->get()->sum('maintenance_count'),
            4 => $this->vessels()->withCount('shipments')->get()->sum('shipments_count'),
            5 => $this->vessels()->first()->owner()->first()->rating,
            6 => $this->date,
        ];
    }

    public function total()
    {
        return $this->payments()->sum('value');
    }

    public function payments()
    {
        return $this->hasMany(RequestResponsePayment::class, 'offer_id');
    }

    public function vessels()
    {
        return $this->belongsToMany(Vessel::class, 'requests_responses_requests_goods_types_vessels', 'offer_id', 'vessel_id')->withPivot('request_good_id');
    }

}
