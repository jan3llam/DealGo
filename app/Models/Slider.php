<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Slider extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];
    protected $table = 'slider';
    protected $appends = ['name_translation'];

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
