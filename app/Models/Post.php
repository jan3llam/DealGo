<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, HasTranslatableSlug;

    public $translatable = ['name', 'description', 'slug'];
    public $timestamps = false;
    protected $appends = ['name_translation', 'meta_file'];
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100)
            ->doNotGenerateSlugsOnUpdate();
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getSlugTranslationAttribute()
    {
        return $this->getTranslation('slug', app()->getLocale());

    }

    public function getMetaFileAttribute()
    {
        return $this->meta_image;

    }
}
