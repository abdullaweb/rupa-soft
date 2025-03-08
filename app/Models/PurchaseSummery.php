<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseSummery extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    public function deduction()
    {
        return $this->belongsTo(StockDeduction::class, 'deduction_id', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(PurchaseSubCategory::class, 'purchase_sub_cat_id', 'id');
    }

}
