<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'port_id',
        'request_id',
        'geo_id',
        'port_type',
        'confirme_type',
        'port_sea',
        'sea_river',
        'sea_draft',
        'air_draft',
        'beam_restriction',
        'loading_conditions',
        'mtone_value',
        'NAABSA',
        'SSHINC',
        'SSHEX',
        'FHINC',
        'FHEX',
    ];


}
