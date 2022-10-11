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
        return ($this->getFullValueAttribute() - $this->payments()->whereNotNull('submit_date')->sum('value'));
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
            return $this->origin->offer_goods_types;
        } elseif ($this->origin instanceof RequestResponse) {
            return $this->origin->request->goods_types;
        }
    }

    public function getGoodsTypesVesselsAttribute()
    {
        if ($this->origin instanceof RequestResponse) {
            $data = $this->origin->vessels;
            foreach ($data as &$item) {
                $rgt = RequestsGoodsType::find($item->pivot->request_good_id) ? RequestsGoodsType::find($item->pivot->request_good_id)->good_id : null;
                if ($rgt) {
                    $item->good_type = gType::find($rgt);
                }
            }
            return $data;
        }
        return null;
    }

}
