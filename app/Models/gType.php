<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class gType extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name'];
    protected $table = 'goods_types';
    protected $appends = ['name_translation'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function vessels_types()
    {
        return $this->belongsToMany(vType::class, 'vessels_goods_types', 'good_id', 'type_id');
    }

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
