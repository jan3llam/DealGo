<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoyageCalculation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'voyage_calculations';

    protected $fillable = [
        'name',
        'details'
    ];

    protected $casts = [
        'details' => 'json',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
