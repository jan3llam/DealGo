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

}
