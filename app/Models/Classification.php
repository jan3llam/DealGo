<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Classification extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name'];
    protected $appends = ['name_translation'];

    public function getNameTranslationAttribute()
    {
        return $this->name;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
