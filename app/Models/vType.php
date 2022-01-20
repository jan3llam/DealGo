<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class vType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vessels_types';

    public function vessels()
    {
        return $this->hasMany(Vessel::class, 'type_id');
    }
}
