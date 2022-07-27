<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offers_responses';
    protected $with = ['tenant.user'];
    protected $appends = ['approved'];

    public function parent()
    {
        return $this->offer();
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function total()
    {
        return $this->payments()->sum('value');
    }

    public function payments()
    {
        return $this->hasMany(OfferResponsePayment::class, 'offer_id');
    }

    public function routes()
    {
        return $this->belongsToMany(Port::class, 'offers_responses_routes', 'offer_id', 'port_id')->withPivot('order');
    }

    public function port_to()
    {
        return $this->belongsTo(Port::class, 'port_to');
    }

    public function goods_types()
    {
        return $this->hasMany(OfferResponseGoodsType::class, 'offer_id');
    }


    public function offer_goods_types()
    {
        return $this->belongsToMany(gType::class, 'offers_responses_goods_types', 'offer_id', 'good_id')->withPivot('weight', 'id');
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

}
