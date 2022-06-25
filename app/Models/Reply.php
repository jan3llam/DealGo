<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    public $timestamps = ["created_at"];
    protected $table = 'tickets_replies';
    protected $with = ['author'];

    public function author()
    {
        return $this->morphTo();
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

}
