<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function origin()
    {
        return $this->morphTo();
    }

    public function getRemainingValueAttribute()
    {
        return ($this->getFullValueAttribute() - $this->payments()->where('paid', 1)->sum('value'));
    }

    public function getFullValueAttribute()
    {
        return intval($this->payments()->sum('value'));
    }

    public function payments()
    {
        return $this->hasMany(ContractPayment::class);
    }

    public function getGoodsTypesAttribute()
    {
        if ($this->origin instanceof OfferResponse) {
            return $this->with('origin.goods_types.good_type');
        } elseif ($this->origin instanceof RequestResponse) {
            return $this->with('origin.request.goods_types.good_type');
        }
    }
}
