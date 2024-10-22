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
        'details',
        'user_id'
    ];

    protected $casts = [
        'details' => 'json',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
