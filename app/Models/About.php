<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class About extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $translatable = ['name', 'description'];
    protected $table = 'about';
    protected $appends = ['name_translation'];

    public function getNameTranslationAttribute()
    {
        return $this->name;
    }
}
