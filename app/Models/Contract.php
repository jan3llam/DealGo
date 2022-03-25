<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments()
    {
        return $this->hasMany(ContractPayment::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function origin()
    {
        return $this->morphTo();
    }
}
