<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestResponseGoodsType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'requests_responses_requests_goods_types_vessels';

    public function good_type()
    {
        return $this->belongsTo(gType::class, 'good_id');
    }

    public function response()
    {
        return $this->belongsTo(OfferResponse::class, 'offer_id');
    }
}
