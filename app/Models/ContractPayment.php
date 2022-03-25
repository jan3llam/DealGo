<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contracts_payments';

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

}
