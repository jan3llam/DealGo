<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferResponsePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offers_responses_payments';

    public function response()
    {
        return $this->belongsTo(OfferResponse::class, 'offer_id');
    }

}
