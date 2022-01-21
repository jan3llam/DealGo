<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;


    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function goods_types()
    {
        return $this->belongsToMany(gType::class, 'tenants_goods_types', 'tenant_id', 'good_id');
    }
}
