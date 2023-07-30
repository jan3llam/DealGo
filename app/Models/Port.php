<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Port extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name'];
    protected $with = ['city.country'];
    protected $appends = ['name_translation'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function localarea()
    {
        return $this->belongsTo(LocalArea::class,'local_area_id','id');
    }
    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'port_from', 'id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'port_from', 'id');
    }

    public function portRequest()
    {
        return $this->hasMany(PortRequest::class);
    }

}
