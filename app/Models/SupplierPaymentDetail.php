<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPaymentDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function payment(){
        return $this->belongsTo(SupplierPaymentDetail::class, 'id', 'purchase_id');
    }
}
