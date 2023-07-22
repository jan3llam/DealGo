<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Models\LocalArea;
 
class GlobalArea extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name'];
    protected $table = 'global_areas';
     protected $appends = ['name_translation'];

     public function localareas()
    {
       
        return $this->hasMany(LocalArea::class,'global_area_id','id');
    }

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
