<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestsGoodsType extends Model
{
    use HasFactory, SoftDeletes;

    public function good_type()
    {
        return $this->belongsTo(gType::class, 'good_id');
    }

//    public function good_type()
//    {
//        return $this->belongsTo(Requestres::class, 'good_id');
//    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
