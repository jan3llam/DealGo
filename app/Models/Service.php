<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, HasTranslatableSlug;

    public $translatable = ['name', 'description', 'slug'];
    protected $appends = ['name_translation'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100)
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getSlugTranslationAttribute()
    {
        return $this->getTranslation('slug', app()->getLocale());
    }
}
