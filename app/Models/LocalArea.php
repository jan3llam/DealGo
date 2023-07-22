<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class LocalArea extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name'];
    protected $table = 'local_areas';
    protected $with = ['globalarea'];
    protected $appends = ['name_translation'];

    public function globalarea()
    {    
        return $this->belongsTo(GlobalArea::class,'global_area_id','id');
    }

    public function ports()
    {
       
        return $this->hasMany(Port::class,'local_area_id','id');
    }

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    
}
