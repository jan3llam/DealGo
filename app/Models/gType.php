<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class gType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'goods_types';

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function vessels_types()
    {
        return $this->belongsToMany(vType::class, 'vessels_goods_types', 'good_id', 'type_id');
    }
}
