<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestResponsePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'requests_responses_payments';

    public function response()
    {
        return $this->belongsTo(RequestResponse::class, 'offer_id');
    }

}
