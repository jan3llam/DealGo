<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'port_id',
        'request_id',
        'goods_id',
        'stowage_factor',
        'min_weight',
        'max_weight',
        'cbm_cbft',
        'min_cbm_cbft',
        'max_cbm_cbft',
        'min_sqm',
        'max_sqm',
    ];

}
