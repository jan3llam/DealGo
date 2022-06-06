<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function port_from()
    {
        return $this->belongsTo(Port::class, 'port_from');
    }

    public function port_to()
    {
        return $this->belongsTo(Port::class, 'port_to');
    }

    public function getGoodsTypesAttribute()
    {
        if ($this->cotract->origin instanceof OfferResponse) {
            return $this->contract->origin->goods_types->each(function ($item) {
                return $item->good_type;
            });
//        } elseif ($this->contract->origin instanceof RequestResponse) {
//            return $this->contract->origin->request->request_goods_types->whereHas('')->each(function ($item) {
//                return $item->good_type;
//            });
        }
    }
}
