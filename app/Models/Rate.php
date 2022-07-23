<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
