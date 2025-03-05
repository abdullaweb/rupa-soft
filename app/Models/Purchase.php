<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function payment(){
        return $this->belongsTo(SupplierPaymentDetail::class, 'id', 'purchase_id');
    }

    public function purchase_meta()
    {
        return $this->belongsTo(PurchaseMeta::class, 'purchase_id', 'id');
    }

    public function purchase_details(){
        return $this->hasMany(InvoiceDetail::class,'invoice_id', 'id');
    }

    public function purchaseAccountDetails()
    {
        return $this->hasOne(AccountDetail::class, 'invoice_id');
    }

}
