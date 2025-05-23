<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function payment(){
        return $this->belongsTo(Payment::class, 'id', 'invoice_id');
    }

    public function invoice_details(){
        return $this->hasMany(InvoiceDetail::class,'invoice_id', 'id');
    }

    public function accountDetails()
    {
        return $this->hasOne(AccountDetail::class, 'invoice_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }


}
