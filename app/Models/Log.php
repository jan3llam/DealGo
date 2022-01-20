<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'api_log';

    protected $fillable = [
        'user_id',
        'language',
        'osType',
        'osType',
        'osVersion',
        'deviceModel',
        'ip',
        'params',
        'method',
        'url',
    ];

    public function getUpdatedAtColumn()
    {
        return null;
    }
}
