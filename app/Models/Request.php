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

    public function ports()
    {
        return $this->belongsTo(Port::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
