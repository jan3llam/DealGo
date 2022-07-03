<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Notification extends Model
{
    use HasFactory;


    protected $appends = [
        'title',
        'text'
    ];

    protected $fillable = [
        'user_id',
        'title_ar',
        'title_en',
        'text_ar',
        'text_en',
        'seen',
        'type',
        'custom_data'
    ];


    public function getPayloadAttribute($value)
    {
        return json_decode($value);
    }

    public function getTitleAttribute()
    {
        return $this->{'title_' . App::getLocale()};
    }

    public function getTextAttribute()
    {
        return $this->{'text_' . App::getLocale()};
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
