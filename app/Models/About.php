<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class About extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $incrementing = false;
    public $translatable = ['name', 'description'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $table = 'about';
    protected $appends = ['name_translation'];

    public function getNameTranslationAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getDescriptionHtmlTranslationAttribute()
    {
        $quill = new \DBlackborough\Quill\Render($this->getTranslation('name', app()->getLocale()));
        return $quill->render();
    }
}
