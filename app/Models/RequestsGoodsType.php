<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestsGoodsType extends Model
{
    use HasFactory, SoftDeletes;

    public function port_to()
    {
        return $this->belongsTo(gType::class, 'good_id');
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
