<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, SoftDeletes;


    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class);
    }

    public function port_from()
    {
        return $this->belongsTo(Port::class, 'port_from');
    }

    public function port_to()
    {
        return $this->belongsTo(Port::class, 'port_to');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Port::class, 'requests_routes', 'request_id', 'port_id')->withPivot('order');
    }

    public function goods_types()
    {
        return $this->belongsToMany(gType::class, 'requests_goods_types', 'request_id', 'good_id')->withPivot('weight', 'id');
    }

    public function origin()
    {
        return $this->morphOne(Contract::class, 'origin');
    }
}
