<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offers_responses';

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

    public function goods_types()
    {
        return $this->belongsToMany(OfferResponseGoodsType::class, 'offers_responses_goods_types', 'offer_id');
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }

}
