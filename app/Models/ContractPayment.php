<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contracts_payments';

    public function getRemainingValueAttribute()
    {
        return ($this->getFullValueAttribute() - $this->where('contract_id', $this->contract_id)->whereNotNull('submit_date')->sum('value'));
    }

    public function getFullValueAttribute()
    {
        return intval($this->where('contract_id', $this->contract_id)->sum('value'));
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

}
