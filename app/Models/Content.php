<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Content extends Model
{
    use HasFactory;

    protected $table = 'content';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'content'
    ];

    public function getContentAttribute()
    {
        return $this->{'content_' . App::getLocale()};
    }
}
