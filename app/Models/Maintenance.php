<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes;

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value);
    }
}
