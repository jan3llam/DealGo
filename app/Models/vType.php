<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class vType extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];
    protected $table = 'vessels_types';
    protected $appends = ['name_translation', 'description_translation'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function vessels()
    {
        return $this->hasMany(Vessel::class, 'type_id');
    }

    public function goods_types()
    {
        return $this->belongsToMany(gType::class, 'vessels_goods_types', 'type_id', 'good_id');
    }

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getDescriptionTranslationAttribute()
    {
        return $this->getTranslation('description', app()->getLocale());
    }
}
