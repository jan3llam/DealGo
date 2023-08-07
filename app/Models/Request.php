<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, SoftDeletes;


    protected $with = ['port_from', 'port_to', 'routes'];
    protected $fillable = [
        'name',
        'port_from',
        'port_to',
        'routes',
        'tenant_id',
        'owner_id',
        'contract',
        'date_from',
        'date_to',
        'description',
        'files',
        'approved',
        'matrix',
        'vessel_category',
        'vessel_category_json',
        'prompt',
        'spot',
        'dead_spot',
        'sole_part',
        'address_commission',
        'broker_commission',
        'part_type',
        'min_weight',
        'max_weight',
        'min_cbm',
        'max_cbm',
        'min_cbft',
        'max_cbft',
        'min_sqm',
        'max_sqm',
        'status_id'
    ];
    protected $dates = ['date_from', 'date_to'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class);
    }

    public function request_goods_types()
    {
        return $this->hasMany(RequestsGoodsType::class);
    }

    public function port_from()
    {
        return $this->belongsTo(Port::class, 'port_from');
    }

    public function port_to()
    {
        return $this->belongsTo(Port::class, 'port_to');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Port::class, 'requests_routes', 'request_id', 'port_id')->withPivot('order');
    }

    public function goods_types()
    {
        return $this->belongsToMany(gType::class, 'requests_goods_types', 'request_id', 'good_id')->withPivot('weight', 'id');
    }

    public function getMatrixAttribute($value)
    {
        return json_decode($value);
    }

    public function portRequest()
    {
        return $this->hasMany(PortRequest::class);
    }

    public function loadRequest()
    {
        return $this->hasMany(LoadRequest::class);
    }

    public function status(){
        return $this->hasOne(Status::class,'id','status_id');
    }
}
