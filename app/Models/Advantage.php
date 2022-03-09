<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Advantage extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];
    protected $appends = ['name_translation'];

    public function getNameTranslationAttribute()
    {
        return $this->name;
    }
}
