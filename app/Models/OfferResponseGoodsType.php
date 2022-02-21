<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferResponseGoodsType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offers_responses_goods_types';

    public function good_type()
    {
        return $this->belongsTo(gType::class, 'good_id');
    }

    public function response()
    {
        return $this->belongsTo(OfferResponse::class, 'offer_id');
    }
}
